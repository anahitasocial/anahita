<?php defined('KOOWA') or die('Restricted access') ?>

<?php
$view = @view('story')->layout('list');

if (isset($actor)) {
    $view->actor($actor);
}
?>

<?php foreach ($stories as $story) : ?>
<?= $view->item($story) ?>
<?php endforeach; ?>
