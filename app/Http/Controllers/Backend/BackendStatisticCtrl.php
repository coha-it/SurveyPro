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
        $response = null;

        foreach ($ids as $i => $val) {
            $id = $ids[$i];
            $survey = $request
                ->user()
                ->allowedSurveys()
                ->find($id)
                ->getSelfWithRelations()
            ;

            echo "Survey: ". $survey['id'] . "\n";
            foreach ($survey->questions->toArray() as $j => $q) {
                $question = Question::with('awnsers')->find($q['id']);
                $awnsers = $question->awnsers;

                echo "  Question: ".$question['id']."\n";
                foreach ($awnsers as $k => $awnser) {
                    $user = User::find($awnser['user_id']);
                    echo "    Awnser from " . $user->id . " - " . $user->pan->pan . "\n";

                    if ($awnser['skipped']) {
                        echo "      -Skipped\n";
                    }

                    if ($comment = $awnser['comment']) {
                        echo "      -Comment: " . $comment . "\n";
                    }
                    if ($options = $awnser['awnser_options']) {
                        foreach ($options as $l => $option) {
                            echo "      -awnser_options: " . $option['title'] . " - " . $option['subtitle'] . "\n";
                        }
                    }
                }
            }

        }


        return $response;
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
