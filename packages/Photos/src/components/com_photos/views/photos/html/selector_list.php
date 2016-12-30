<? defined('KOOWA') or die('Restricted access');?>

<? if (count($photos)) :?>
<? foreach ($photos as $photo) : ?>
<div class="thumbnail-wrapper" photo="<?= $photo->id ?>">
	<a data-trigger="MediaViewer" class="thumbnail-link" href="<?= $photo->getPortraitURL('original') ?>" title="<?= @escape($photo->title) ?>">
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES) ?>
		<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
	</a>
</div>
<? endforeach; ?>
<? else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
