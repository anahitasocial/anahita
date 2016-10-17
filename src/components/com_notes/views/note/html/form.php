<? defined('KOOWA') or die ?>


<form action="<?= @route($item->getURL()) ?>" method="post" >
	<fieldset>
		<legend><?= @text('COM-NOTES-NOTE-EDIT') ?></legend>
			<div class="control-group">
				<div class="controls">
						<textarea
								required
								name="body"
								class="input-block-level"
								maxlength="5000"
								rows="5"
								cols="25"
						><?= @escape($item->body) ?></textarea>
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
	</fieldset>
</form>
