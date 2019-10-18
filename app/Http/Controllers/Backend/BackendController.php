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
        return $request->user()->users->each(function ($i, $k) {
            $i->pan->makeVisible(['pin']);
        })->toJson();
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
         // $token = $this->getRandom("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 6);
            $token = $this->getRandom("123456789ABCDEFGHIJKLMNPQRSTUVWXYZ", 6);
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

            // Get pan and pin
            $user = $user->getSelfWithPanUserRelations();
            $user->pan->makeVisible(['pin', 'pan', 'pan.pin']);

            array_push(
                $arr,
                $user
            );
        }

        return $arr;
    }

    /**
     * Update a User which was created by another User (self)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateCreatedUsers(Request $request) {
        
        // Get Data
        $self = $request->user();
        

        // Validate Data
        $validator = \Validator::make($request->all(), [
            'users' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->errors(), 
                    'user' => $user->toJson()
                ],
                400
            );
        }

        $users = $request->users;
        $response = [];
        foreach ($users as $key => $reqUser) {
            // Validate Data
            $validator = \Validator::make($reqUser, [
                'pan.pan' => 'required|unique:u_pans,pan,'. $request->user['id'] .',user_id',
                'pan.pin' => 'required|min:4|max:4'
            ]);

            $user = $self->users->find($reqUser['id']);

            // If Group is in Request
            if($user->groups) {
                
                $aSync = [];

                // Go through requestes User-Groups
                foreach ($reqUser['groups'] as $group) {
                    // If Self has Rights
                    if ( $self->groupsModerating->find($group['id']) ) {
                        $aSync[$group['id']] = [
                            'is_mod' => $group['pivot']['is_mod'],
                            'is_member' => $group['pivot']['is_member']
                        ];
                    }
                }

                $user->groups()->sync($aSync);

            }

            // Update Data
            $user->update($reqUser);
            $user->pan()->update($reqUser['pan']);
            array_push($response, $user->toJson());
        }

        return $response;
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
