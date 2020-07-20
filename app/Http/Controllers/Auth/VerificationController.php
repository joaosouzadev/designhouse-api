<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user) {
        if (!URL::hasValidSignature($request)) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Link invalido'
                    ]
                ], 422
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Email ja foi verificado'
                    ]
                ], 422
            );
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email verificado com sucesso'], 200);
    }

    public function resend(Request $request) {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Nao foi encontrado usuario com este email'
                    ]
                ], 422
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'Email ja foi verificado'
                    ]
                ], 422
            );
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email de verificacao reenviado'], 200);
    }
}
