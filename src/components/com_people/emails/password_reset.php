<?php defined('ANAHITA') or die; ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-PASSWORD-RESET'), $person->name) ?> 
<?= @route('option=com_people&view=session&reset_password=1&token='.$person->activationCode) ?>
