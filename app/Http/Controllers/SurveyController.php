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

    // Get the Fillable Surveys
    public function getFillableSurveys(Request $request) {
        return json_encode(
            $request->user()->fillableSurveys()->toArray()
        );
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
