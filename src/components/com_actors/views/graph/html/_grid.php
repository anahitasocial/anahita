<? defined('KOOWA') or die ?>

<div class="media-grid">
	<? foreach ($actors as $actor) : ?>
	<div><?= @avatar($actor) ?></div>
	<? endforeach; ?>
</div>
