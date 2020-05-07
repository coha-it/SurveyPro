<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailCtrl extends Controller
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
     * Send Contact-Mail for Pan-User
     *
     * @return void
     */
    public function sendEntranceMail(Request $request) {

        // Validate Data
        $request->validate([
            'id' => 'required',
        ]);

        // Get Created User with Relation
        $panUser = $request->user()->users->find($request->id)->getSelfWithRelations($request->id);

        // Mail::raw('Hallo Welt', function($message)
        // {
        //     $message->from('it@corporate-happiness.de', env('APP_NAME'));
        //     $message->subject('Test-Mail');
        //     $message->to('a.bachschmid@corporate-happiness.de');
        // });

        // Update User with sending Mail
        $panUser->pan['last_mail_date'] = now()->toDateTimeString();
        $panUser->pan['last_mail_status'] = 'success';

        // Save PAN
        $panUser->pan->save();
    }
}
