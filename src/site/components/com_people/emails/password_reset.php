<?= sprintf(@text('COM-PEOPLE-PASSWORD-RESET-BODY'), $user->name ) ?>

<?= @route('option=com_people&view=people&reset_password=1&token='.$user->activation) ?>




