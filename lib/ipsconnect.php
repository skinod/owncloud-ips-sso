<?php

/**
 * This class contains all hooks.
 */

namespace OCA\user_ipsconnect;

class IpsConnect {
	private static $masterUrl = "";
	private static $masterKey = "";
	private static $inited = false;
	public static function init() {
		if($inited) {
			return;
		}
		self::$masterUrl = \OC_Config::getValue( "user_ipsconnect_master_url" );
		self::$masterKey = \OC_Config::getValue( "user_ipsconnect_master_key" );
		$inited = true;
	}
	public static function chkCookies() {
		if(!\OC_User::isLoggedIn() and $_COOKIE[ 'ipsconnect_' . md5( self::$masterUrl ) ] ) {
			$check = file_get_contents( self::$masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'cookies', 'data' => json_encode( $_COOKIE ) ) ) ));
			$check = trim(substr($check, strpos($check, "{")));
			if ( $check = @json_decode( $check, TRUE ) and $check['connect_status'] == 'SUCCESS' )
			{
				if ( \OC_User::userExists( $check['connect_username'] ) == false )
				{
					// Create local member
					\OC_User::createUser($check['connect_username'], \OC_Util::generateRandomBytes(16));
				}

				// set connect_id
				if(!self::getConnectID($check['connect_username'])) {
					self::setConnectID($check['connect_username'], $check['connect_id']);
				}

				// print_r("sdasda"); exit;
				// replace successfully used token with a new one
				\OC_User::unsetMagicInCookie();
				\OC_Preferences::deleteKey($check['connect_username'], 'login_token', $_COOKIE['oc_token']);
				$token = \OC_Util::generateRandomBytes(32);
				\OC_Preferences::setValue($check['connect_username'], 'login_token', $token, time());
				\OC_User::setMagicInCookie($check['connect_username'], $token);
				// login
				\OC_User::setUserId($check['connect_username']);
				\OC_User::setDisplayName($check['connect_username']);
				\OC_User::getUserSession()->setLoginName($check['connect_username']);
			}
		}elseif(\OC_User::isLoggedIn() and $_COOKIE[ 'ipsconnect_' . md5( self::$masterUrl ) ] == 0 ) {
			\OC_User::logout();
		}
	}

	public static function deleteUser($uid) {
		$check = file_get_contents( self::$masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'delete', 'ids[]' => $uid ) ) )); // fix me, missing the key
		$check = trim(substr($check, strpos($check, "{")));
		return ( $check = @json_decode( $check, TRUE ) and $check['status'] == 'SUCCESS' );
	}

	public static function setPassword( $uid, $password ) {
		if($connect_id = self::getConnectID($uid)) {
			$check = file_get_contents( self::$masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'change', 'password' => md5($password), 'key' => md5( self::$masterUrl . $connect_id ), ) ) ));
			$check = trim(substr($check, strpos($check, "{")));
			return ( $check = @json_decode( $check, TRUE ) and $check['status'] == 'SUCCESS' );
		}
	}

	public static function userExists( $uid ) {
		$check = file_get_contents( self::$masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'check', 'key' => self::$masterUrl, 'username' => $uid, 'displayname' => $uid ) )));
		return ( $check = @json_decode( $check, TRUE ) and $check['status'] == 'SUCCESS' and $check['username'] and $check['displayname'] );
	}

	public static function checkPassword( $uid, $password ) {
		$login = file_get_contents( self::$masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'login', 'idType' => 'username', 'id' => $uid, 'password' => md5( $password ) ) )));
		$login = trim(substr($login, strpos($login, "{")));
		if ( $login = @json_decode( $login, TRUE ) and $login['connect_status'] == 'SUCCESS' ) {
			return true;
		}
		return false;
	}

	public static function setConnectID($uid, $ipsconnect_id) {
		if ( \OC_User::userExists( $uid ) == true ) {
			$query = \OC_DB::prepare('UPDATE `*PREFIX*users` SET `ipsconnect_id` = ? WHERE LOWER(`uid`) = LOWER(?)');
			$result = $query->execute(array($ipsconnect_id,$uid));
			return true;
		}
		return false;
	}

	public static function getConnectID($uid) {
		if ( \OC_User::userExists( $uid ) == true ) {
			$query = \OC_DB::prepare('SELECT `ipsconnect_id` FROM `*PREFIX*users` WHERE LOWER(`uid`) = LOWER(?)');
			$row = $query->execute(array($uid))->fetchRow();
			return $row['ipsconnect_id']?$row['ipsconnect_id']:false;
		}
		return false;
	}
}









