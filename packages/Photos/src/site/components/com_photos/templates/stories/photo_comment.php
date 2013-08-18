<?php defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-PHOTO-COMMENT'), @name($subject), @route($object->getURL())) ?>
</data>

<?php if ($type != 'notification') :?>
<data name="body">
	<?php if( !empty($object->title) ): ?>
	<h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
	<?php endif; ?>

	<div data-behavior="Mediabox">
		<?php 
			$rel = 'lightbox[actor-'.$object->owner->id.' 900 900]';
		
			$caption = htmlspecialchars($object->title, ENT_QUOTES).
			(($object->title && $object->description) ? ' :: ' : '').
			@helper('text.script', $object->description);			 
		?>
		<a rel="<?= $rel ?>" href="<?= @route($object->getPortraitURL('medium')) ?>" title="<?= $caption ?>">
			<img class="entity-portrait-medium" src="<?= $object->getPortraitURL('medium') ?>" />
		</a>
	</div>
</data>
<?php endif;?>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">	
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td valign="top" style="padding-right:10px">
                <a href="<?= @route($object->getURL().'&permalink='.$comment->id) ?>">
        			<img width="100" src="<?= $object->getPortraitURL('square') ?>" />
        		</a>            
            </td>
            <td valign="top">
                <?= $comment->body?>
            </td>
    </table>	
</data>
<?php endif;?>