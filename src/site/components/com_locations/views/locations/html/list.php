<?php defined('KOOWA') or die; ?>

<?php $view = @view('location'); ?>
<?php foreach($locations as $location) : ?>
<?= $view->layout('list')->location($location) ?>
<?php endforeach; ?>
