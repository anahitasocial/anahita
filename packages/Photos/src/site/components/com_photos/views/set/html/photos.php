<?php defined('KOOWA') or die ?>

<?php $photos = $set->photos->order('photoSets.ordering'); ?>

<?php foreach($photos as $photo) :?>
<div class="thumbnail-wrapper <?= ($set->isCover($photo)) ? 'cover' : '' ?>" photo="<?= $photo->id ?>">
	<a data-trigger="MediaViewer" class="thumbnail-link" href="<?= $photo->getPortraitURL('original') ?>" title="<?= @escape($photo->title) ?>">
		<?php $caption = htmlspecialchars($photo->title, ENT_QUOTES) ?>
		<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
	</a>
</div>
<?php endforeach; ?>



