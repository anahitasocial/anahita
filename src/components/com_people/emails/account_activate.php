<?php defined('KOOWA') or die; ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-ACCOUNT-ACTIVATE'), $person->name)?> 
<?= @route('option=com_people&view=session&token='.$person->activationCode) ?>
