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

    public function updateOrCreateQuestionOptions($question, $options)
    {
        // Go Through all Requestet Question-Options
        foreach ($options as $j => $reqOption)
        {
            // Update or Create the QuestionOptions
            $question->options()->updateOrCreate(
                ['id' => $reqOption['id'] ?? 0],
                $reqOption
            );
        }
    }

    public function updateOrCreateQuestions($self, $survey, $reqQuestions)
    {
        // Go Through all Requested Questions
        foreach ($reqQuestions as $i => $reqQuestion)
        {
            // Update or Create the Question
            $tmp = $reqQuestion;
            unset($tmp['options']);
            $question = $survey->questions()->updateOrCreate(
                ['id' => $reqQuestion['id'] ?? 0],
                $tmp
            );

            $this->updateOrCreateQuestionOptions( $question, $reqQuestion['options'] ?? [] );
        }
    }

    public function updateOrCreateSurvey($self, $reqSurvey)
    {
        // Update
        if( $survey = $self->allowedSurveys()->find($reqSurvey['id'] ?? 0) )
        {
            $survey->update($reqSurvey);
        }
        // Or Create
        else
        {
            $survey = $self->createdSurveys()->create($reqSurvey);
        }

        // Connect survey with group
        $this->connectSurveyAndGroups($self, $survey, $reqSurvey['groups'] ?? []);

        // UpdateOrCreate the questions (and options)
        $this->updateOrCreateQuestions($self, $survey, $reqSurvey['questions'] ?? []);

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
                $survey = $this->updateOrCreateSurvey($self, $reqSurvey);
                return $survey->getSelfWithRelations()->toJson();
            }


            // Return Error
            return back()->withErrors('your error message');
        }

        // 3.2 If no ID is in Params
        else {
            //  - then create a survey
            $survey = $this->updateOrCreateSurvey($self, $reqSurvey);
            return $survey->getSelfWithRelations()->toJson();
        }
    }


    public function deleteQuestions(Request $request)
    {
        // Validate
        $request->validate([
            'ids' => 'required|array'
        ]);

        // Variables
        $self = $request->user();

        // Go Through all Sended Question-IDs
        // $self->questions->find($id)->delete();
        return $self->allowedQuestions->find($request->ids)->toJson();
    }

}
