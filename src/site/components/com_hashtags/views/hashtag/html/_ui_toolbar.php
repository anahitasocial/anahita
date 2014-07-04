<?php defined('KOOWA') or die('Restricted access');?>

<div class="btn-toolbar clearfix" data-behavior="BS.Dropdown">
    <div class="pull-right btn-group">
        <a class="btn <?= ($sort == 'top') ? 'disabled' : '' ?>" href="<?= @route(array('view'=>'hashtag', 'alias'=>$item->name, 'sort'=>'top')) ?>">
            <i class="icon-fire"></i>
            <?= @text('COM-HASHTAGS-HASHTAGABLES-SORT-TOP') ?>
        </a> 
        <a class="btn <?= ($sort != 'top') ? 'disabled' : '' ?>" href="<?= @route(array('view'=>'hashtag', 'alias'=>$item->name, 'sort'=>'recent')) ?>">
            <i class="icon-time"></i>
            <?= @text('COM-HASHTAGS-HASHTAGABLES-SORT-RECENT') ?>
        </a>
    </div>
</div>