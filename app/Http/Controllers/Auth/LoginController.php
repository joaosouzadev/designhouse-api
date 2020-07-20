<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function attemptLogin(Request $request) {
        $token = $this->guard()->attempt($this->credentials($request));

        if (!$token) {
            return false;
        }

        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return false;
        }

        $this->guard()->setToken($token);
        return true;
    }

    protected function sendLoginResponse(Request $request) {
        $this->clearLoginAttempts($request);

        $token = (string)$this->guard()->getToken();

        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ], 200);
    }

    protected function sendFailedLoginResponse(Request $request) {
        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Você precisa verificar seu e-mail'
                    ]
                ], 422
            );
        }

        throw ValidationException::withMessages([
            $this->username() => 'Credenciais inválidas'
        ]);
    }

    public function logout() {
        $this->guard()->logout();

        return response()->json(['message' => 'Usuário fez logout']);
    }
}
