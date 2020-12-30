<?php defined('ANAHITA') or die; ?>
<? $settings = AnService::get('com:settings.config') ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-ACCOUNT-ACTIVATE'), $person->name)?> 

<? if ($settings->client_domain) : ?>
<?= sprintf('%s/token/%s/', $settings->client_domain, $person->activationCode) ?>
<? else : ?>
<?= @route('option=com_people&view=session&token='.$person->activationCode) ?>
<? endif ?>