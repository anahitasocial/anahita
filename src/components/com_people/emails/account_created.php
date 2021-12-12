<?php defined('ANAHITA') or die; ?>
<? $settings = AnService::get('com:settings.config'); ?>
<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-ACCOUNT-CREATED'), $person->name) ?> 
<?= sprintf('%s/token/%s/resetpassword/', $settings->client_domain, $person->activationCode) ?>
