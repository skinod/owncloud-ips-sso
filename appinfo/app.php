<?php

/**
 * Copyright (c) 2014 Sajjad Hashemian <info@skinod.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

\OCA\user_ipsconnect\ipsconnect::init();
\OCA\user_ipsconnect\ipsconnect::chkCookies();

// setup hooks, we need to logout and sign-in for cookies and assign connect_id
OCP\Util::connectHook('OC_User', 'post_login', '\OCA\user_ipsconnect\Hooks', 'postLogin');
OCP\Util::connectHook('OC_User', 'logout', '\OCA\user_ipsconnect\Hooks', 'logout');

require_once OC_App::getAppPath('user_ipsconnect').'/user_ipsconnect.php';

OC_APP::registerAdmin('user_ipsconnect', 'settings');

OC_User::registerBackend("IPSCONNECT");
OC_User::useBackend( "IPSCONNECT" );

// add settings page to navigation
$entry = array(
	'id' => "user_ipsconnect_settings",
	'order'=>1,
	'href' => OC_Helper::linkTo( "user_ipsconnect", "settings.php" ),
	'name' => 'IPSCONNECT'
);
