<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Question;
use App\User;

class BackendStatisticCtrl extends Controller
{

    public function getSurveysStatistics (Request $request) {
        $ids = is_array($request->ids) ? $request->ids : [$request->ids];
        $statistics = [
            "data" => [],
            "header" => []
        ];

        foreach ($ids as $i => $val) {
            $id = $ids[$i];
            $survey = $request
                ->user()
                ->allowedSurveys()
                ->find($id)
                ->getSelfWithRelations()
            ;

            // echo "Survey: ". $survey['id'] . "\n";
            foreach ($survey->questions->toArray() as $j => $q) {
                $question = Question::with('awnsers')->find($q['id']);
                $awnsers = $question->awnsers;

                $questionKey = $question['title']." (#".$question['id'].")";

                $statistics['data'][$questionKey] = [];
                // $statistics['header'][] = [
                //     'name' => $questionKey,
                //     'label' => $questionKey,
                //     'field' => $questionKey,
                //     'sortable' => true,
                //     'align' => 'left'
                // ];

                // Build Header
                if ($j === 0) {
                    foreach ($awnsers as $k => $awnser) {
                        $statistics['header'][] = " User" .$awnser['user_id'];
                    }
                }


                // echo "  Frage: ". $questionKey . "\n";
                foreach ($awnsers as $k => $awnser) {
                    $user = User::find($awnser['user_id']);
                    $userAwnser = "Nutzer: (".$user->pan->pan.")";
                    // echo "    " . $userAwnser . "\n";

                    $statistics['data'][$questionKey][$userAwnser] = "";
                    $awnserContent = "";

                    if ($awnser['skipped']) {
                        // Skipped
                        $awnserContent = "Übersprungen";
                        // echo "      -".$awnserContent."\n";
                    } else {
                        // Not skipped
                        if ($options = $awnser['awnser_options']) {
                            foreach ($options as $l => $option) {
                                $awnsr = $option['title'] . " - " . $option['subtitle'];
                                // echo "      -awnser_options: " . $awnsr . "\n";
                                $awnserContent .= $awnsr;
                            }
                        }

                        if ($comment = $awnser['comment']) {
                            $cmnt = "Comment: " . $comment;
                            // echo "      -" . $cmnt . "\n";
                            $awnserContent .= $comment;
                        }
                    }



                    $statistics['data'][$questionKey][$userAwnser] = $awnserContent;
                }
            }

        }


        return $statistics;
    }

    public function search($a, $b) {
        return strpos(strtolower($a), strtolower($b)) !== false;
    }

    public function getAllowedFilteredSurveys(Request $request) {
        $search = $request->search;
        return $request
                ->user()
                ->allowedSurveys()
                ->filter(function ($value, $key) use ($search) {
                    if(!$search) return true;

                    switch ($search) {
                        case $value->id == $search:
                        case $this->search($value->title, $search):
                            return true;
                            break;

                        default:
                            return false;
                            break;
                    }
                })
                ->map
                ->only([
                    'id',
                    'title',
                    'desc_short',
                    'desc_long'
                ])
                ->take(5)
                ->toArray();
    }
}
