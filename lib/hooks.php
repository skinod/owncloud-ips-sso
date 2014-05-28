<?php

/**
 * This class contains all hooks.
 */

namespace OCA\user_ipsconnect;

class Hooks {
	public static function postLogin($uid) {
		$masterUrl = \OC_Config::getValue( "user_ipsconnect_master_url" );
		$masterKey = \OC_Config::getValue( "user_ipsconnect_master_key" );

		$login = file_get_contents( $masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'login', 'idType' => 'username', 'id' => $uid['uid'], 'password' => md5( $_POST['password'] ) ) )));
		$login = trim(substr($login, strpos($login, "{")));
		if ( $login = @json_decode( $login, TRUE ) and $login['connect_status'] == 'SUCCESS' ) {
			if(!\OCA\user_ipsconnect\ipsconnect::getConnectID($login['connect_username'])) {
				\OCA\user_ipsconnect\ipsconnect::setConnectID($login['connect_username'], $login['connect_id']);
			}
			if(!empty($_REQUEST['redirect_url'])) {
				$location = \OC_Helper::makeURLAbsolute(urldecode($_REQUEST['redirect_url']));
			}
			if (strpos($location, '@') !== false or empty($location)) {
				$location = \OC_Helper::makeURLAbsolute(\OC::$WEBROOT);
			}
			$redirect = base64_encode( $location );
			header( 'Location: ' . $masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'login', 'idType' => 'username', 'id' => $uid['uid'], 'password' => md5($_POST['password']), 'key' => md5( $masterKey . $uid['uid'] ), 'redirect' => $redirect, 'redirectHash' => md5( $masterKey . $redirect ), 'noparams' => '1' ) ) ));
			exit;
		}

	}

	public static function logout() {
		if(!$_GET['nipc']) {
			$masterUrl = \OC_Config::getValue( "user_ipsconnect_master_url" );
			$masterKey = \OC_Config::getValue( "user_ipsconnect_master_key" );
			$redirect = link_to('', 'index.php') . '?logout=true&nipc=1"';
			$connect_id = \OCA\user_ipsconnect\ipsconnect::getConnectID(\OC::$session->get('user_id'));
			header( 'Location: ' . $masterUrl . '?' . html_entity_decode(http_build_query( array( 'act' => 'logout', 'id' => $connect_id, 'key' => md5( $masterKey . $connect_id ), 'redirect' => base64_encode( $redirect ), 'redirectHash' => md5( $masterKey . base64_encode( $redirect ) ) ) ) ));
			exit;
		}
	}
}









