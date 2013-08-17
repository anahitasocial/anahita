<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ( $type != 'notification') : ?>	
	<?php if( $object->access != 'public' && ($target->id == $subject->id || $target->eql($actor)) ): ?>
    <i class="icon-lock"></i>  
    <?php endif; ?>
    <?= sprintf(@text('COM-NOTES-STORY-ADD'), @name($subject), @route($object->getURL())) ?>
<?php else: ?>
    <?php if ( $type == 'notification' ) : ?>	
	<?= sprintf(@text('COM-NOTES-STORY-ADD'), @name($subject), @route($object->getURL())) ?>    
    <?php endif; ?>
<?php endif; ?>
</data>

<?php if ( $type == 'story') : ?>
<data name="body">    
	<?= @helper('text.truncate', @content($object->body), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>
<?php else : ?>
<data name="email_body">
<div><?= $object->body ?></div>
<?php $commands->insert('viewstory', array('label'=>@text('COM-NOTES-VIEW-POST')))->href($object->getURL())?>
</data>
<?php endif;?>

