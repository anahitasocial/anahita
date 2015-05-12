<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?=sprintf(@text('COM-STORIES-TITLE-UPDATE-COVER'), @name($subject)) ?>
</data>

<data name="body">
    <?= @cover($target, 'medium') ?>
</data>
