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

    public function createGroup(Request $request) 
    {
        $request->validate([
            'item' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;
        $self = $request->user();

        $g = Group::create([
            'name' => $item['name'],
            'description_public' => $item['description_public'],
            'description_mods' => $item['description_mods'],
            'created_by' => $self->id,
        ]);

        $g->moderators()->sync([
            $self->id => [    
                'is_mod' => 1,
                'is_member' => 0
            ],
        ]);

        return $g->toJson();
    }

    // Update Compnay Location Department
    public function updateGroup(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.id' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;

        $m = $request->user()->groupsModerating()->find($item['id']);
        $m->name = $item['name'];
        $m->description_public = $item['description_public'];
        $m->description_mods = $item['description_mods'];
        $m->save();

        return $m->toJson();
    }

    /**
     * Just Create a Random PIN with 0-9 and 4 Characters
     * 
     * @return String $token
     */
    public function getRandom($pattern, $max) {
        return substr(str_shuffle(str_repeat($pattern, $max)), 0, $max);
    }

    /**
     * Just Create a Random String with A-Z and 0-9 and 6 Characters
     * 
     *
     * @return String $token
     */
    public function getRandomPan() {
        do {
            $token = $this->getRandom("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 6);
            $user = Pan::where('pan', $token)->get();
        }
        while(!$user->isEmpty());

        return $token;
    }

    /**
     * Just Create a Random PIN with 0-9 and 4 Characters
     * 
     * @return String $token
     */
    public function getRandomPin() {
        return $this->getRandom("0123456789", 4);
    }

    /**
     * Create Many Users by number
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createUsers(Request $request) {
        // Validate Data
        $request->validate([
            'number' => 'required|min:1|max:100',
        ]);

        // Variables
        $number = $request->number;
        $self = $request->user();
        $arr = [];

        // Go Through Number
        for ($i=0; $i < $number; $i++) { 
            $sRandPan = $this->getRandomPan();
            $sRandPin = $this->getRandomPin();

            $user = User::create(['created_by' => $self->id]);
            $user->pan()->updateOrCreate([
                'pan' => $sRandPan,
                'pin' => $sRandPin
            ]);
            $user->pan->save();
            $user->save();

            array_push(
                $arr,
                $user->getSelfWithPanUserRelations()
            );
        }

        return $arr;
    }

    /**
     * Upda a User which was created by the User
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Delete User which was created by the User
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request) {

        // Validate Data
        $request->validate([
            'ids' => 'required',
        ]);

        // Get Data
        $userIds = $request->ids;
        $deletedIds = [];
        $self = $request->user();

        for ($i=0; $i < count($userIds); $i++) { 
            // Find
            $id = $userIds[$i];
            $user = $self->users->find($id);
            
            // Delete
            array_push($deletedIds, $user->id);
            $user->pan->delete();
            $user->delete();
        }

        return "";
    }

}
