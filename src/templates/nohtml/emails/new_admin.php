<?php defined('ANAHITA') or die; ?>

<? $settings = AnService::get('com:settings.config') ?>
<? $personURL = sprintf('%s/people/%/', $settings->client_domain, $person->alias) ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-NEW-ADMIN'), $personURL, $person->name)?>