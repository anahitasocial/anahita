<?php defined('KOOWA') or die('Restricted access') ?>

<?php foreach( $sets as $set) : ?>
<?= @view('set')->layout('list')->set('set', $set) ?>
<?php endforeach; ?>
