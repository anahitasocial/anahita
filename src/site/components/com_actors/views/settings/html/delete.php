<?php defined('KOOWA') or die ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-DELETE') ?></h3>

<form action="<?= @route($item->getURL()) ?>" method="post">
	<input type="hidden" name="action" value="delete" />	
	
	<div class="alert alert-warning">
  		<p><?= sprintf(translate(array($item->component.'-DELETE-PROMPT','COM-ACTORS-DELETE-PROMPT'))) ?></p>
	</div>
	
	<div class="form-actions">
		<button type="submit" data-trigger="DeleteActor" class="btn btn-danger">
  	        <?= @text('LIB-AN-ACTION-DELETE') ?>
  		</button>
	</div>
</form>