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

        // Send Mail
        Mail::send('emails.entrance', ['user' => $panUser], function ($mail) use ($panUser) {
            $mail
                ->from('it@corporate-happiness.de', env('APP_NAME'))
                ->to($panUser->pan->contact_mail)
                ->subject('Welcome - Your Entrance-Informatio');
        });

        $response = '';
        if( count(Mail::failures()) > 0 ) {

            $response .= "There was one or more failures. They were: <br />";

            foreach(Mail::failures() as $email_address) {
                $response .= " - $email_address <br />";
             }

         } else {
             $response .= "Success";
         }

        // Update User with sending Mail
        $panUser->pan['last_mail_status'] = $response;
        $panUser->pan['last_mail_date'] = now()->toDateTimeString();

        // Save PAN
        $panUser->pan->save();
    }
}
