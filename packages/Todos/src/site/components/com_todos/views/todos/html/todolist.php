<?php defined('KOOWA') or die; ?>

<div id="entity-add-wrapper" class="hide">
<?= @view('todo')->layout('form')->actor($actor) ?>
</div>

<?= @helper('ui.filterbox',@route('layout=list')) ?>

<div class="an-entities" id="an-entities-main">
<?= @template('list') ?>
</div>