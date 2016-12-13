<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    protected function guard()
    {
        return Auth::guard();
    }

    public function confirmEmail(Request $request)
    {
        $id = $request->query('id');
        $confirmationCode = $request->query('confirmation');

        $user = User::find($id);

        $validUntil = $user->confirmation_code_valid_until;

        if ($validUntil < Carbon::now()) {
            Session::flash('error', 'Something went wrong! Please request the confirmation email again.');
            return redirect()->to('/login');
        }

        if ($user->confirmation_code === $confirmationCode) {
            $user->confirmed = true;
            $user->save();
            Session::flash('success', 'Yo dawg!! Yo creds is lit!');
            return redirect()->to('/login');
        } else {
            $request->flash('error', 'Something went wrong!');
        }

        return redirect()->to('/login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user->confirmed) {
            $request->session()->flash('error', 'Please confirm your email first.');
            return redirect()->back();
        }

        $this->guard()->login($user);

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/home');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}
