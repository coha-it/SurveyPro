<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserNewsletter as Newsletter;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);


        if($request->newsletter === false) {
            $user->newsletter()->delete();
        }
        else if($request->newsletter === true) {
            $user->newsletter()->updateOrCreate([
                'user_id' => $request->user()->id
            ]);
        }

        // Update!
        $user->update($request->toArray());

        // Return User!
        return $user->getSelfWithRelations();
    }
}
