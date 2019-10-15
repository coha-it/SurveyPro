<?php

namespace App\Http\Controllers\Backend;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Survey as Survey;

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
            'auth.user.email', 
            'auth.user.can_create_surveys'
        ]);
    }

    // Get the Created Surveys
    public function getCreatedSurveys(Request $request) {
        return $request->user()->surveys->toJson();
    }

    // Get the Created Survey
    public function getCreatedSurvey(Request $request) {
        // Validate
        $request->validate(
            ['id' => 'required|int|min:1']
        );

        // Search Survey
        $survey = $request->user()->surveys->find($request->id);
        return $survey->getSelfWithRelations()->toJson();
    }

    // Update the Created Survey
    public function tryUpdateCreatedSurvey(Request $request) {
        // 1. Validate the Requests
        $request->validate([
            'survey' => 'required',
            'survey.start_datetime' => 'required|date_format:Y-m-d H:i:s',
            'survey.end_datetime' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $reqSurvey = $request->survey;
        $self = $request->user();

        // 3. Check if Request has ID
        if( array_key_exists('id', $reqSurvey) && $survey = $self->surveys->find( $reqSurvey['id'] ) ) {
            // 3.1 If ID exists and survey found - Check if Survey is editable
            if($survey->isEditable())
            {

                // Go through requeste's User-Groups
                $aSync = [];
                foreach ($reqSurvey['groups'] as $group) {
                    // If Self has Rights
                    if ( $self->groupsModerating->find($group['id']) ) {
                        array_push($aSync, $group['id']);
                    }
                }
                $survey->groups()->sync($aSync);

                // Update Survey
                $survey->update($reqSurvey);

                // Return the Survey
                return $survey->getSelfWithRelations()->toJson();
            }


            // Return Error
            return back()->withErrors('your error message');
        }

        // 3.2 If no ID is in Params
        else {
            //  - then create a survey
            $survey = $self->surveys()->create($reqSurvey);
            return $survey->toArray();
        }
    }

}
