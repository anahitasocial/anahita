<?php defined('KOOWA') or die('Restricted access'); ?>

<?php if( !empty($header_text) ) : ?>
<div class="header-text"><?= @content($header_text) ?></div>
<?php endif; ?>

<?php if(count($actors)) : ?>
<?= @template($actor_layout) ?>
<?php else : ?>
<div class="alert alert-info"><?= @text('LIB-AN-NO-RECORD-AVAILABLE') ?></div>
<?php endif; ?>

 