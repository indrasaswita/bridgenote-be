<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository {
	public function __construct() {
		$this->user = new User();
	}

	public function getUserById($user_id){
		$user = $this->user
			->find($user_id);

		return $user;
	}

	public function getUserByEmail($email, $status=null) {
		$user = $this->user
			->where('email', 'LIKE', $email);

		if($status) {
			$user = $user
				->where('status', $status);
		}

		$user = $user
			->first();

		return $user;
	}

	public function saveUser($data){
		$user = $this->user
			->create($data);

		return $user;
	}

	public function upgradePosition($user_id){
		$user = $this->user
			->find($user_id);

		if($user == null)
			return 403;

		$currentPosition = (int) str_replace("Level ", "", $user->position);
		if($currentPosition == 5)
			return 403;

		$user->position = "Level ".($currentPosition + 1);
		$result = $user->save();
		return $result ? 200 : 500;
	}
}