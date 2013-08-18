<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">        	
    <?php if ( $object->access != 'public' ) : ?>
        <i class="icon-lock"></i>
    <?php endif;?>		
    <?= sprintf(@text('COM-NOTES-STORY-ADD'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">    
	<div class="entity-body">
		<?= @helper('text.truncate', @content($object->body), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>
</data>
<?php if ($type == 'notification') :?>
<data name="email_body">
<div><?= $object->body ?></div>
<?php $commands->insert('viewstory', array('label'=>@text('COM-NOTES-VIEW-POST')))->href($object->getURL())?>
</data>
<?php endif;?>

