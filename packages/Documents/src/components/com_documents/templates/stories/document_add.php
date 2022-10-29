<? defined('ANAHITA') or die('Restricted access');?>

<? if (is_array($object)) : ?>
<data name="title">
	<?= sprintf(@text('COM-DOCUMENTS-STORY-NEW-DOCUMENTS'), @name($subject)) ?>
</data>
<? else: ?>
<data name="title">
	<?= sprintf(@text('COM-DOCUMENTS-STORY-NEW-DOCUMENT'), @name($subject), @route($object->getURL())); ?>
</data>
<? endif;?>

<? if ($type != 'notification') : ?>
<data name="body">
	<? if (!is_array($object)) : ?>
		<? $caption = htmlspecialchars($object->title, ENT_QUOTES) ?>
		<? if (!empty($object->title)): ?>
		<h4 class="entity-title">
    		<a href="<?= @route($object->getURL()) ?>">
    			<?= $object->title ?>
    		</a>
    	</h4>
		<? endif; ?>

		<? if ($story->body) : ?>
		<div class="entity-description">
			<?= nl2br($story->body) ?>
		</div>
		<? endif;?>
	<? endif; ?>
</data>
<? else : ?>
<? $commands->insert('viewpost', array('label' => @text('COM-DOCUMENTS-DOCUMENT-VIEW')))->href($object->getURL())?>
<? endif;?>
