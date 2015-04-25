<?php defined('KOOWA') or die ?>

<form action="<?= @route($item->getURL()) ?>" method="post" >

	<div class="control-group">
		<div class="controls">
			<input type="text" class="input-block-level" name="title" value="<?= @escape( $item->title ) ?>" size="50" maxlength="255" required>
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
		<textarea name="body" class="input-block-level" maxlength="5000" rows="5" cols="25"><?= @escape( $item->body ) ?></textarea>	
		</div>
	</div>
	
	<div class="form-actions">
		<a data-trigger="EditableCancel" class="btn" href="<?= @route($item->getURL()) ?>">
		    <?= @text('LIB-AN-ACTION-CANCEL') ?>
		</a> 
		
		<button type="submit" class="btn btn-primary">
		    <?= @text('LIB-AN-ACTION-UPDATE') ?>
		</button>
	</div>
</form>