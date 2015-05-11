<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?=sprintf(@text('COM-STORIES-TITLE-UPDATE-COVER'), @name($subject), @possessive($target)) ?>
</data>

<data name="body">
    <?= @cover($target, 'medium') ?>
</data>
