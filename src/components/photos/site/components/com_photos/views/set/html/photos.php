<?php defined('KOOWA') or die ?>

<?php $photos = $set->photos->order('photoSets.ordering'); ?>

<div id="set-mediums" class="set-mediums an-entities">
	<div class="media-grid">
		<?php foreach($photos as $photo) :?>
		<div class="thumbnail-wrapper <?= ($set->isCover($photo)) ? 'cover' : '' ?>" mid="<?= $photo->id ?>">
			<a class="thumbnail-link" href="<?= @route($photo->getURL()) ?>" title="<?= @escape($photo->title) ?>">
				<?php 
				$caption = htmlspecialchars($photo->title, ENT_QUOTES).
				(($photo->title && $photo->description) ? ' :: ' : '').
				@helper('text.script', $photo->description)
				?>
				<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
			</a>
		</div>
		<?php endforeach; ?>
	</div>
</div>