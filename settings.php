<?php

/**
 * Copyright (c) 2014 Sajjad Hashemian <info@skinod.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */


OC_Util::checkAdminUser();

if($_POST) {
	// CSRF check
	OCP\JSON::callCheck();

	if(isset($_POST['master_url']) && isset($_POST['master_key'])) {
		OC_CONFIG::setValue('user_ipsconnect_master_url', strip_tags($_POST['master_url']));
		OC_CONFIG::setValue('user_ipsconnect_master_key', strip_tags($_POST['master_key']));
	}
}

// fill template
$tmpl = new OC_Template( 'user_ipsconnect', 'settings');
$tmpl->assign( 'master_url', OC_Config::getValue( "user_ipsconnect_master_url" ));
$tmpl->assign( 'master_key', OC_Config::getValue( "user_ipsconnect_master_key" ));

return $tmpl->fetchPage();
