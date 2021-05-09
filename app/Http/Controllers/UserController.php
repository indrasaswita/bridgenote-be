<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\UserService;

class UserController extends Controller
{
	public function __construct(){
		$this->userService = new UserService();
	}

	public function getProfile(Request $request){
		$rules = [
		];
		$customMessages = [];
		$customAttributes = [];
		$request->validate($rules, $customMessages, $customAttributes);

		return $this->userService->getProfile($request);
	}

	public function login(Request $request){
		$rules = [
			'username' => 'required|email',
			'password' => 'required|string|min:4',
		];
		$customMessages = [];
		$customAttributes = [];
		$request->validate($rules, $customMessages, $customAttributes);

		return $this->userService->login($request);
	}

	public function register(Request $request){
		$rules = [
			'name' => 'required|string|min:2',
			'email' => [
				'required', 
				'email', 
				\Illuminate\Validation\Rule::unique('users', 'email')
					->where('status', 'Active'),
			],
			'password' => 'required|string|min:4',
		];
		$customMessages = [];
		$customAttributes = [];
		$request->validate($rules, $customMessages, $customAttributes);

		return $this->userService->register($request);
	}

	public function upgradePosition(){
		return $this->userService->upgradePosition();
	}

	public function removeUser(Request $request){
		$rules = [
			'type' => 'required|string|in:delete,update',
			'user_id' => 'required|integer|exists:users,id',
		];
		$customMessages = [];
		$customAttributes = [];
		$request->validate($rules, $customMessages, $customAttributes);

		return $this->userService->removeUser($request);
	}

}
