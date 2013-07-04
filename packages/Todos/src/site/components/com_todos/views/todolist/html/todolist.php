<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar')?>

<div class="an-entity">
	<h3 class="entity-title">
		<?= @escape($todolist->title) ?>
	</h3>
	
	<?php if($todolist->description): ?>
	<div class="entity-description">
		<?= @content( $todolist->description ) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todolist->id ?>">
			<?= @helper('ui.voters', $todolist); ?>
		</div>
	</div>
</div>