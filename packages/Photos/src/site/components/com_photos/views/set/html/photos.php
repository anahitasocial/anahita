<?php defined('KOOWA') or die ?>

<?php $photos = $set->photos->order('photoSets.ordering'); ?>

<?php $index = 0; ?>
<?php foreach($photos as $photo) :?>
<div class="thumbnail-wrapper <?= ($index == 0) ? 'cover' : ''; ?>" photo="<?= $photo->id ?>">
	<a data-trigger="MediaViewer" class="thumbnail-link" href="<?= $photo->getPortraitURL('original') ?>" title="<?= @escape($photo->title) ?>">
		<?php $caption = htmlspecialchars($photo->title, ENT_QUOTES, 'UTF-8') ?>
		<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
	</a>
</div>
<?php $index++; ?>
<?php endforeach; ?>



