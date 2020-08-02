<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Http\Resources\UserResource;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Repositories\Contracts\UserInterface;

class SettingsController extends Controller
{
    protected $userInterface;

    public function __construct(UserInterface $userInterface) {
        $this->userInterface = $userInterface;
    }

    public function updateProfile(Request $request) {
    	$user = auth()->user();

    	$this->validate($request, [
    		'tagline' => ['required'],
    		'name' => ['required'],
    		'about' => ['required', 'string', 'min:20'],
    		'formatted_address' => ['required'],
    		'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
    		'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
    	]);

    	$location = new Point($request->location['latitude'], $request->location['longitude']);

    	$user = $this->userInterface->update(auth()->id(),
            [
        		'name' => $request->name,
        		'tagline' => $request->tagline,
        		'about' => $request->about,
        		'formatted_address' => $request->formatted_address,
        		'location' => $location
        	]
        );

    	return new UserResource($user);
    }

    public function updatePassword(Request $request) {
    	$user = auth()->user();

    	$this->validate($request, [
    		'current_password' => ['required', new MatchOldPassword],
    		'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword]
    	]);
    	
    	$user = $this->userInterface->update(auth()->id(),
            [
    		  'password' => bcrypt($request->password)
            ]
    	);

    	return response()->json([
    		'message' => 'Senha atualizada'
    	], 200);
    }
}
