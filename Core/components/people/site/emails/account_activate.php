
<?= @text('Hi')?> <?= $user->name ?>


<?= @text('COM-PEOPLE-PASSWORD-RESET-BODY')?>

<?= @route('option=com_people&view=person&token='.$user->activation) ?>
