<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class MeController extends Controller
{
    public function getMe() {
    	if (auth()->check()) {
    		return new UserResource(auth()->user());
    	} else {
    		return response()->json(null, 401);
    	}
    }
}
