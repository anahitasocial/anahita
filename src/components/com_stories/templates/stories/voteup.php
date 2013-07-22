<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ( isset($comment)) : ?>
<?= sprintf(@text('COM-STORIES-COMMENT-VOTEUP'), @name($subject), @route($comment->parent->getURL().'#permalink='.$comment->id))?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($comment->parent->getURL().'&permalink='.$comment->id)?>
<?php else : ?>
<?= sprintf(translate(array($object->component.'-VOTEUP-'.$object->getIdentifier()->name,'COM-STORIES-VOTEUP-POST')), @name($subject), @possessive($object->author), @route($object->getURL()));	?>
<?php $lable = translate(array($object->component.'-VIEW-'.$object->getIdentifier()->name,'COM-STORIES-VIEW-POST'))?>
<?php $commands->insert('viewpost', array('label'=>$lable))->href($object->getURL())?>
<?php endif;?>
</data>



