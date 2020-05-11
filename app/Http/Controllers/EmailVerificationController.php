<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use App\Notifications\EmailVerificationNotification;
use Mail;
use App\Exceptions\InvalidRequestException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        //take paras from url : "email" as key | "token" as value
        $email = $request->input('email');
        $token = $request->input('token');

        //if any of them is empty, means verification link is wrong. Throw exception
        if (!$email || !$token) {
            throw new InvalidRequestException('Verification Link is incorrect.');
        }

        //if load those two from cache, compare 'token' from both cache and url
        //if cache's 'token' is null or unmatch with the url's 'token',
        //then throw exception.
        if ($token != Cache::get('email_verification_'.$email)) {
            throw new InvalidRequestException('Verification Link is incorrect or expired.');
        }

        //Search user form database according email address
        if (!$user = User::where('email', $email)->first()) {
            throw new InvalidRequestException('User is not exist.');
        }

        //Delete key from cache.
        Cache::forget('email_verification_'.$email);

        //Change 'email_verified' value to 'true'
        $user->update(['email_verified' => true]);

        //Notify user that email verification is success
        return view('pages.success', ['msg' => 'Email verified successfully!']);
    }

    public function send(Request $request)
    {
        $user = $request->user();

        //check whether it is verified
        if ($user->email_verified) {
            throw new InvalidRequestException('Email address already verified.');
        }

        //call notify() method to send notification class
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => 'Email send successfully.']);
    }
}
