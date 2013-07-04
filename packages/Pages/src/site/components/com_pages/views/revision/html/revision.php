<?php defined('KOOWA') or die ?>

<?php @commands('toolbar')?>

<div class="an-entity an-revision-main">
	<h1 class="entity-title">
		<?= @escape($revision->title) ?>
	</h1>
	
	<?php if($revision->body): ?>
	<div class="entity-description">
		<?= @content( $revision->body ) ?>
	</div>
	<?php endif; ?>
</div>

