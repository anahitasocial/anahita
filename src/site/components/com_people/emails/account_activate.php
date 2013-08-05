<?= sprintf(@text('COM-PEOPLE-ACTIVATION-LINK-SENT'), $user->name )?>

<?= @route('option=com_people&view=person&token='.$user->activation) ?>
