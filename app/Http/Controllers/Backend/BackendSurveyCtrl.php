<?php

namespace App\Http\Controllers\Backend;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Survey as Survey;
use App\Question as Question;

class BackendSurveyCtrl extends Controller
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

    // Get the Allowed Surveys
    public function getAllowedSurveys(Request $request) {
        return $request->user()->allowedSurveys()->toJson();
    }

    // Get the Allowed Survey
    public function getAllowedSurvey(Request $request) {
        // Validate
        $request->validate(
            ['id' => 'required|int|min:1']
        );

        // Search Survey
        $survey = $request->user()->allowedSurveys()->find($request->id);
        if($survey) {
            return $survey->getSelfWithRelations()->toJson();
        }
    }

    public function connectSurveyAndGroups($self, $survey, $aGroups) {
        // Go through requeste's User-Groups
        $aSync = [];
        foreach ($aGroups as $group)
        {
            // If Self has Rights
            if ( $self->groupsModerating->find($group['id']) ) {
                array_push($aSync, $group['id']);
            }
        }
        $survey->groups()->sync($aSync);
    }

    public function updateOrCreateQuestionOptions($bIsNew, $question, $reqOptions)
    {
        // Go Through all Requestet Question-Options
        foreach ($reqOptions as $j => $reqOption)
        {
            // Remove Dialog
            unset($reqOption['dialog']);

            // Request Option Id
            $reqOptionId = array_key_exists('id', $reqOption) ? $reqOption['id'] : null;

            // Create if New
            if($bIsNew) {
                unset($reqOptionId);
                $question->options()->updateOrCreate($reqOption);
            }
            // Update if not
            else {
                $question->options()->updateOrCreate(
                    ['id' => $reqOptionId],
                    $reqOption
                );
            }

        }
    }

    public function updateOrCreateQuestions($self, $survey, $reqQuestions)
    {
        // Go Through all Requested Questions
        foreach ($reqQuestions as $i => $reqQuestion)
        {
            // Update or Create the Question
            $tmp = $reqQuestion;
            $bIsNew = array_key_exists('is_new', $reqQuestion) ? $reqQuestion['is_new'] : false;

            unset($tmp['options']);
            unset($tmp['is_new']);

            // Req Question Id
            $reqQuestionId = array_key_exists('id', $reqQuestion) ? $reqQuestion['id'] : null;

            // If Is New
            if($bIsNew) {
                unset($tmp['id']);
                $tmp['created_by'] = $self->id;
                $question = $survey->questions()->updateOrCreate($tmp);
            }
            else {
                $question = $survey->questions()->updateOrCreate(
                    ['id' => $reqQuestionId],
                    $tmp
                );
            }

            $this->updateOrCreateQuestionOptions( $bIsNew, $question, $reqQuestion['options'] ?? [] );
        }
    }

    public function updateOrCreateSurvey($self, $request)
    {
        // Get Request-Survey
        $reqSurvey = $request->survey;

        // Update
        if( $survey = $self->allowedSurveys()->find($reqSurvey['id'] ?? 0) ) {
            $survey->update($reqSurvey);
        }
        // Or Create
        else {
            $survey = $self->createdSurveys()->create($reqSurvey);
        }

        // Connect survey with group
        $this->connectSurveyAndGroups($self, $survey, $reqSurvey['groups'] ?? []);

        // UpdateOrCreate the questions (and options)
        $this->updateOrCreateQuestions($self, $survey, $reqSurvey['questions'] ?? []);

        // Try to Delete some Questions Options
        $this->deleteQuestionOptions($survey, $request);

        // Try to Delete Some Questions
        $this->deleteQuestions($survey, $request);

        // Return the Survey
        return $survey;
    }

    // Update the Created Survey
    public function tryUpdateAllowedSurvey(Request $request)
    {
        // 1. Validate the Requests
        $request->validate([
            'survey' => 'required',
            'survey.start_datetime' => 'required|date_format:Y-m-d H:i:s',
            'survey.end_datetime' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $reqSurvey = $request->survey;
        $self = $request->user();

        // 3. Check if Request has ID
        if(
            array_key_exists('id', $reqSurvey) &&
            $survey = $self->allowedSurveys()->find( $reqSurvey['id'])
        ) {
            // 3.1 If ID exists and survey found - Check if Survey is editable
            if($survey->isEditable())
            {
                $survey = $this->updateOrCreateSurvey($self, $request);
                return $survey->getSelfWithRelations()->toJson();
            }


            // Return Error
            return back()->withErrors('your error message');
        }

        // 3.2 If no ID is in Params
        else {
            //  - then create a survey
            $survey = $this->updateOrCreateSurvey($self, $request);
            return $survey->getSelfWithRelations()->toJson();
        }
    }

    public function deleteQuestions($survey, $req)
    {
        // Go Through all Sended Question-IDs
        $oDeleteQuestions   = $survey->questions->find($req['delete_questions_ids'] ?? []);

        // Delete Questions
        foreach ($oDeleteQuestions as $question)
        {
            // Try to Delete Question
            $question->delete();
        }
    }

    public function deleteQuestionOptions($survey, $req)
    {
        $aDeleteOptions = $req['delete_options_ids'] ?? [];

        // Go Through all Question-IDs and try to delete an deleted Option
        foreach ($survey->questions as $question)
        {
            // Try to Delete Options
            foreach($aDeleteOptions as $id)
            {
                $option = $question->options->find($id);
                if($option) $option->delete();
            }
        }
    }



}
