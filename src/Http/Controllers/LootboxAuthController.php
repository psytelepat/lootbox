<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Psytelepat\Lootbox\View\LoginTemplate;

class LootboxAuthController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        if (Auth::user()) {
            return redirect()->route('lootbox.dashboard');
        }

        return new LoginTemplate;
    }

    public function postLogin(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->to('/');
    }

    public function redirectTo()
    {
        return config('lootbox.user.redirect', route('lootbox.dashboard'));
    }
}
