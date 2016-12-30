<? defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-SET'),  @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
	<? if (!empty($object->title)): ?>
	<h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
	<? endif; ?>

	<? if ($object->description): ?>
	<div class="entity-description">
	    <?= @content(nl2br($object->description), array('exclude' => 'gist')) ?>
	</div>
	<? endif; ?>

	<div class="media-grid">
		<? $photos = $object->photos->order('photoSets.ordering')->limit(10)->fetchSet(); ?>
		<? foreach ($photos as $i => $photo): ?>
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES); ?>
		<? if ($i > 12) {
    break;
} ?>
		<div class="entity-portrait">
			<a data-rel="story-<?= $story->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">
				<img src="<?= $photo->getPortraitURL('square') ?>" />
			</a>
		</div>
	    <? endforeach; ?>
	</div>

	<div class="entity-meta">
		<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $object->getPhotoCount()) ?>
	</div>
</data>
