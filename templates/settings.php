<form id="ipsconnect" action="#" method="post">
	<fieldset class="personalblock">
		<h2><?php p($l->t('IPS Connect'));?></h2>
		<p><label for="master_url"><?php p($l->t('Master URL: '));?><input type="url" id="master_url" name="master_url" value="<?php p($_['master_url']); ?>"></label></p>
		<p><label for="master_key"><?php p($l->t('Master KEY: '));?><input type="text" id="master_key" name="master_key" value="<?php p($_['master_key']); ?>"></label></p>
		 <input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
		<input type="submit" value="Save" />
	</fieldset>
</form>
