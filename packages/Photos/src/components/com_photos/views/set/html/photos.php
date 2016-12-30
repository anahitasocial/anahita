<? defined('KOOWA') or die ?>

<? $photos = $set->photos->order('photoSets.ordering'); ?>

<? $index = 0; ?>
<? foreach ($photos as $photo) :?>
<div class="thumbnail-wrapper <?= ($index == 0) ? 'cover' : ''; ?>" photo="<?= $photo->id ?>">
	<a data-trigger="MediaViewer" class="thumbnail-link" href="<?= $photo->getPortraitURL('original') ?>" title="<?= @escape($photo->title) ?>">
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES) ?>
		<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
	</a>
</div>
<? ++$index; ?>
<? endforeach; ?>
