<? defined('KOOWA') or die ?>

<? @commands('toolbar')?>

<div class="an-entity">
	<h1 class="entity-title">
		<?= @escape($revision->title) ?>
	</h1>

	<? if ($revision->body): ?>
	<div class="entity-description">
		<?= @content($revision->body) ?>
	</div>
	<? endif; ?>
</div>
