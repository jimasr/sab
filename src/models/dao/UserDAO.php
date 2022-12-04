<?php

require_once PATH_CORE . 'DAO.php';
require_once PATH_ENTITIES . 'User.php';
require_once PATH_ENTITIES . 'Admin.php';
require_once PATH_ENTITIES . 'Creator.php';

class UserDAO extends DAO {

	public function __construct() {
		parent::__construct('users');
	}

	public function getByEmail(string $email): User | false {
		
		$sql = "SELECT *
				FROM {$this->getTable()} U
				LEFT JOIN admins A
					ON (U.user_id = A.ADMIN_ID)
				LEFT JOIN creators C
					ON (U.user_id = C.CREATOR_ID)
				WHERE U.user_email = ?";
		
		$row = $this->queryRow($sql, [$email], false);
		
		switch (true) {
			case !$row:
				return false;
			
			case $row['admin_id']:
				return new Admin($row);
			
			case $row['creator_id']:
				return new Creator($row);
				
			default:
				return new User($row);
		}
	}

	public function insertUser(User $user): string | false {
		return $this->insert([
			'user_first_name' => $user->getFirstName(),
			'user_last_name' => $user->getLastName(),
			'user_password_hash' => $user->getPasswordHash(),
			'user_email' => $user->getEmail(),
			'user_picture_url' => $user->getPictureUrl()
		], false);
	}

	public function updatePictureUrl(User $user): bool {
		return $this->update($user->getId(), ['user_picture_url' => $user->getPictureUrl()]);
	}
	
}