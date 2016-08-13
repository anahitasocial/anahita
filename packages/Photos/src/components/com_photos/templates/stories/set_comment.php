<? defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
<?= sprintf(@text('COM-PHOTOS-STORY-NEW-SET-COMMENT'),  @name($subject), @route($object->getURL().'&permalink='.$comment->id)) ?>
</data>

<? if ($type != 'notification') :?>
<data name="body">
	<? if (!empty($object->title)): ?>
	<h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
	<? endif; ?>

	<div class="an-meta">
		<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $object->getPhotoCount()) ?>
	</div>
</data>
<? endif;?>

<? if ($type == 'notification') :?>
<? $commands->insert('viewcomment', array('label' => @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id) ?>
<data name="email_body">
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td valign="top" style="padding-right:10px">
                <a href="<?= @route($object->getURL().'&permalink='.$comment->id) ?>">
        			<img width="100" src="<?= $object->getCoverSource('square') ?>" />
        		</a>
            </td>
            <td valign="top">
               <?= nl2br($comment->body) ?>
            </td>
    </table>
</data>
<? endif;?>
