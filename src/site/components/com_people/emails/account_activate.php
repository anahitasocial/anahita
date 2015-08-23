<?php defined('KOOWA') or die; ?>

<?= sprintf(@text('COM-PEOPLE-MAIL-BODY-ACCOUNT-ACTIVATE'), $user->name )?>

<?= @route('option=com_people&view=session&account_activate=1&token='.$user->activation) ?>
