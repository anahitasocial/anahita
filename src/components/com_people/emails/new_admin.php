<?php defined('ANAHITA') or die; ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-NEW-ADMIN'), @route($person->getURL()), $person->name)?>