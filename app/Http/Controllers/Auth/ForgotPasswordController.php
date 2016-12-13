<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $email = $request->get('email');
        $user = User::where('email', $email)->first();

        if ($user && !$user->confirmed) {
            Session::flash('error', 'You haven\'t confirmed your mail yet. Do this first.');
            return redirect()->back();
        }

        $minutes = 30;
        $token = str_random(32);
        $validUntil = Carbon::now()->addMinutes($minutes);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'valid_until' => $validUntil,
            'validated' => false
        ]);

        return view('auth.emails.reset')->with([
            'email' => $email,
            'token' => $token,
            'minutes' => $minutes
        ]);
    }

    public function __construct()
    {
        $this->middleware('guest');
    }
}
