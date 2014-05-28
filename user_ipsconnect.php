<?php


class OC_USER_IPSCONNECT extends OC_User_Backend {
	public function deleteUser($uid) {
		return false;
		return \OCA\user_ipsconnect\ipsconnect::deleteUser($uid);
	}

	public function setPassword ( $uid, $password ) {
		return \OCA\user_ipsconnect\ipsconnect::setPassword($uid, $password);
	}

	public function userExists( $uid ) {
		return \OCA\user_ipsconnect\ipsconnect::userExists($uid);
	}

	public function checkPassword( $uid, $password ) {
		if(\OCA\user_ipsconnect\ipsconnect::checkPassword( $uid, $password )) {
			return $uid;
		}else{
			return false;
		}
	}	

	/**
	 * @return bool
	 */
	public function hasUserListings() {
		return false;
	}

	/*
	* we don´t know the users so all we can do it return an empty array here
	*/
	public function getUsers($search = '', $limit = 10, $offset = 0) {
		return array();
	}
}
