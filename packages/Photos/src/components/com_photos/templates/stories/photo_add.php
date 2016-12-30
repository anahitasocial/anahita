<? defined('KOOWA') or die('Restricted access');?>

<? if (is_array($object)) : ?>
<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-PHOTOS'), @name($subject)) ?>
</data>
<? else: ?>
<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-PHOTO'), @name($subject), @route($object->getURL())); ?>
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
			<?= @content(nl2br($story->body), array('exclude' => 'gist')) ?>
		</div>
		<? endif;?>

		<div class="entity-portrait-medium">
			<a data-rel="story-<?= $story->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $object->getPortraitURL('original'); ?>">
				<img src="<?= $object->getPortraitURL('medium') ?>" />
			</a>
		</div>
	<? else : ?>
	<div class="media-grid">
		<? foreach ($object as $i => $photo) : ?>
		<? if ($i > 12) {
    break;
} ?>
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES); ?>
		<div class="entity-portrait">
			<a data-rel="story-<?= $story->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">
				<img src="<?= $photo->getPortraitURL('square') ?>" />
			</a>
		</div>
		<? endforeach; ?>
	</div>
	<? endif; ?>
</data>
<? else : ?>
<data name="body">
	<div>
		<a href="<?= @route($object->getURL())?>">
			<img src="<?= $object->getPortraitURL('medium') ?>" />
		</a>
	</div>
</data>
<? $commands->insert('viewpost', array('label' => @text('COM-PHOTOS-PHOTO-VIEW')))->href($object->getURL())?>
<? endif;?>
