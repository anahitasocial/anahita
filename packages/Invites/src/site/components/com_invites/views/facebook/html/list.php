
<?php 
$controller = @service('com://site/people.controller.person')
    ->layout('list')
    ->view('people');
$controller->getState()->setList($social_inviter->getPeople());
?>
<?= $controller->getView()->display() ?>
