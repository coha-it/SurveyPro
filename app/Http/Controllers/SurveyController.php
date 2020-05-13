<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SurveyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
        ]);
    }

    // Get the Allowed Surveys
    public function getAllowedSurveys(Request $request) {
        return json_encode(
            $request->user()->allowedSurveys()->toArray()
        );
    }

    // Get the Membering Surveys
    public function getMemberingSurveys(Request $request) {
        return json_encode(
            $request->user()->memberingSurveys()
        );
    }

    // Get the Membering and Fillable Surveys
    public function getFillableSurvey(Request $request) {
        return json_encode(
            $request->user()->fillableSurvey($request->id)
        );
    }

    public function updateOrCreateAwnser($question, $request) {
        // If requested Awnser was found
        $reqAw              = $request->awnser;
        $reqAw['user_id']   = Auth()->user()->id;
        unset($reqAw['awnser_options']); // Remove Useless

        // If Question is Skippable
        if(!$question->is_skippable) {
            // Not Skippable
            $reqAw['skipped'] = 0;
        }

        // Update Or Create
        $awnser = $question->usersAwnser()->updateOrCreate(
            ['id' => $reqAw['id'] ?? 0],
            $reqAw
        );

        // If Question is skippable AND skipped
        if($question->is_skippable && $awnser->skipped) {
            // Awnser is Skipped
            $this->syncWithOptions($awnser);
        } else {
            // Create and De-/Re- Connect
            $this->syncWithOptions($awnser, $request->awnser['awnser_options']); // Connect selected Options with Awnser's Options
        }

        // REturn the Awnser
        return $awnser;
    }

    public function syncWithOptions($awnser, $arr = []) {
        $awnser->awnser_options()->sync(
            array_column($arr, 'id')
        );
    }

    public function httpUpdateOrCreateAwnser(Request $request) {
        // Validate Data
        $request->validate([
            'survey_id' => 'required',
            'question_id' => 'required',
            'awnser' => 'required'
        ]);

        // Find Variables like self or Survey
        $self       = $request->user();
        $survey     = $self->fillableSurvey($request->survey_id);
        $question   = $survey->question($request->question_id);

        // Update or Create the Awnser
        $awnser = $this->updateOrCreateAwnser($question, $request); // Update Awnser

        // Variables
        return $question->usersAwnser()->find($awnser->id)->toJson();
    }

    // // Get the Allowed Survey
    // public function getAllowedSurvey(Request $request) {
    //     // Validate
    //     $request->validate(
    //         ['id' => 'required|int|min:1']
    //     );

    //     // Search Survey
    //     $survey = $request->user()->allowedSurveys()->find($request->id);
    //     if($survey) {
    //         return $survey->getSelfWithRelations()->toJson();
    //     }
    // }

}
