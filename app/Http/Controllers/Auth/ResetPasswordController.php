<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswordsTrait;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @POST /password/reset
     *
     * 1. Reset password for given email and token
     * 2. Authenticate and redirect to secured area
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required', 'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $email = $request->get('email');
        $results = DB::table('password_resets')->where('email', $email)->get();

        if (!$results->isEmpty()) {

            $pair = $results->first();

            if ($pair->token === $request->get('token')) {

                $user = User::where('email', $email)->firstOrFail();
                $user->password = bcrypt($request->get('password'));
                $user->save();
                $this->guard()->login($user);
                return redirect($this->redirectTo);
            }

        }

        Session::flash('error', 'Something went wrong :( Try reset again.');
        return redirect()->back();
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->get('email')]
        );
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
