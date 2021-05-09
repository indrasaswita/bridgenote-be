<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Http\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Reply, Helper, JWTAuth;
use Illuminate\Support\Facades\Auth;

class UserService {
	public function __construct() {
		$this->userRepository = new UserRepository();
	}

	public function getProfile(Request $request){
		$user = Auth::user();

		if($user == null)
			return Reply::do(false, 'Cannot get your profile data.', ['user' => null], __FUNCTION__, 500);

		return Reply::do(true, 'Success', ['user' => $user], __FUNCTION__);
	}

	public function upgradePosition(){
		$user = Auth::user();
		if($user == null)
			return Reply::do(false, 'Need to be logged-in', null, __FUNCTION__);

		$result = $this->userRepository
			->upgradePosition($user->id);

		if($result == 200){
			$user = $this->userRepository
				->getUserById($user->id);

			return Reply::do(true, 'Success', [
				'user' => $user
			], __FUNCTION__);
		}else if($result == 403) {
			return Reply::do(false, 'You are in highest position or any bad data.', null, __FUNCTION__, $result);
		}else {
			return Reply::do(false, 'Cannot upgrade position', null, __FUNCTION__, $result);
		}
	}

	public function register(Request $request) {
		$data = [
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'status' => 'Active',
			'position' => 'Level 1',
		];

		$user = $this->userRepository
			->saveUser($data);

		if($user) {
			return Reply::do(true, 'Success', ['user' => $user], __FUNCTION__);
		} else {
			return Reply::do(false, 'Failed to Register', null, __FUNCTION__);
		}
	}

	public function login(Request $request){
		$email = $request->username;
		$pass = $request->password;

		$user_active = $this->userRepository
			->getUserByEmail($email, 'Active'); // get the active only account

		if($user_active)
			$user = $user_active;
		else
			$user = $this->userRepository
				->getUserByEmail($email);		

		if($user == null) {
			return Reply::do(false, "Email not found", null, __FUNCTION__);
		} else if($user->status !== "Active") {
			return Reply::do(false, "This account is inactive. Please contact administrator to activation.", null, __FUNCTION__);
		}

		$user->makeVisible(['password']);

		if(Hash::check($pass, $user->password)) {
			$token = JWTAuth::fromUser($user);
			$user->makeHidden(['password']);

			return Reply::do(true, "Success", [
				'token' => $token,
				'user' => $user,
			], __FUNCTION__);
		} else {
			return Reply::do(false, "Wrong Password", null, __FUNCTION__);
		}
	}

	public function removeUser(Request $request){
		$type = $request->type;
		$user_id = $request->user_id;

		$result = null;
		if($type == "delete") {
			$result = $this->userRepository
				->deleteUser($user_id);
		} else if($type == "update") {
			$result = $this->userRepository
				->setUserInactive($user_id);
		}

		if($result) {
			return Reply::do($result == 200 ? true : false, $result == 200 ? 'Success' : 'Failed', null, __FUNCTION__);
		} else {
			return Reply::do(false, 'Error exists', null, __FUNCTION__, 500);
		}
	}
}