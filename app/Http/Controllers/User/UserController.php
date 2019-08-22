<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserCompany as Company;

class UserController extends Controller
{
    /**
     * Get the user's profile as JSON itself.
     * 
     * Examples:
     * return $request->user();
     * return App\User::with(['pan', 'right'])->find($request->user()->id); 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function self(Request $request)
    {
        return $request->user()->getSelfWithRelations();
    }

    public function getAllCompanies(Request $request) 
    {
        return $request->user()->createdCompanies->toArray();
    }

    public function createCompany(Request $request) 
    {
        $company = Company::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
        ]);
        return $company->id;
    }

    public function setCompanyId(Request $request)
    {
        $usr = $request->user();
        $usr->company_id = $request->id;
        $usr->save();
    }

    public function updateCompany(Request $request)
    {
        $company = $request->user()->createdCompanies->where('id', '=', $request->id)->first();
        $company->name = $request->name;
        $company->save();
    }
}
