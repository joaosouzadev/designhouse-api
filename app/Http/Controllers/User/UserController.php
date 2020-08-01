<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserInterface;

class UserController extends Controller
{
	protected $userInterface;

	public function __construct(UserInterface $userInterface) {
		$this->userInterface = $userInterface;
	}

	public function index() {
		$users = $this->userInterface->all();
		return UserResource::collection($users);
	}
}
