<?php defined('KOOWA') or die; ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-ACCOUNT-CREATED'), $user->name) ?>
<?= @route('option=com_people&view=session&token='.$user->activation) ?>