<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User as User;
use App\Group as Group;
use App\UserPan as Pan;

class BackendController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'auth.user.email']);
    }

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

    public function getCreatedUsers(Request $request) {
        return $request->user()->users->toJson();
    }

    public function getGroupsModerating(Request $request) {
        return $request->user()->groupsModerating->toJson();
    }

    // public function addUserToGroup(Request $request) {
    //     // Get
    //     $group = $request->user()->groupsModerating->find($request->group_id);
    //     $user = $request->user()->users->find($request->user_id);
    //     // Sync!
    //     $group->users()->attach($user);
    // }

    // public function removeUserFromGroup(Request $request) {
    //     // Get
    //     $self = $request->user();
    //     $group = Group::find($request->group_id);
    //     $user = $self->users->find($request->user_id);
    //     // Sync!
    //     $group->users()->detach($user);
    // }

    public function updateCreatedUser(Request $request) {
        // Validate Data
        $validator = \Validator::make($request->all(), [
            'user' => 'required',
            'user.pan.pan' => 'required|unique:u_pans,pan,'. $request->user['id'] .',user_id',
            'user.pan.pin' => 'required|min:4|max:4'
        ]);
      
        // Get Data
        $self = $request->user();
        $user = $self->users->find($request->user['id']);

        // Execute Validation
        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->errors(), 
                    'user' => $user->toJson()
                ],
                400
            );
        }

        // If Group is in Request
        if($user->groups) {
            // Get
            $groups = $self->groupsModerating->find(
                array_column($request->user['groups'], 'id')
            );

            $user->groups()->sync($groups);
        }

        // Update Data
        $user->update($request->user);
        $user->pan()->update($request->user['pan']);

        // Send User as Response
        return $user->toJson();


    }

}
