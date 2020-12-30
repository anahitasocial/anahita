<?php defined('ANAHITA') or die; ?>
<? $settings = AnService::get('com:settings.config') ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-PASSWORD-RESET'), $person->name) ?> 

<? if ($settings->client_domain) : ?>
<?= sprintf('%s/token/%s/resetpassword/', $settings->client_domain, $person->activationCode) ?>
<? else : ?>
<?= @route('option=com_people&view=session&reset_password=1&token='.$person->activationCode) ?>
<? endif ?>
