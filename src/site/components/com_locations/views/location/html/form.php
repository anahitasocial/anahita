<?php defined('KOOWA') or die; ?>

<?php $location = empty($location) ? @service('repos:locations.location')->getEntity()->reset() : $location; ?>

<form action="<?= @route($location->getURL()) ?>" method="post">
		<fieldset>
				<legend>
						<?= ($location->persisted()) ? @text('LIB-AN-ACTION-EDIT') : @text('LIB-AN-ACTION-ADD') ?>
				</legend>
				<div class="control-group">
						<label class="label-group"  for="entity-name">
						<?= @text('LIB-AN-ENTITY-NAME') ?>
						</label>
						<div class="controls">
						<input required class="input-block-level" id="entity-name" size="30" maxlength="100" name="name" value="<?= $location->name ?>" type="text" />
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="entity-description">
						<?= @text('LIB-AN-ENTITY-DESCRIPTION') ?>
						</label>
						<div class="controls">
						<textarea required maxlength="1000" id="entity-description" class="input-block-level" name="body" rows="5"><?= $location->body?></textarea>
						</div>
				</div>

				<div class="form-actions">
						<a href="javascript:history.go(-1)" class="btn">
						<?= @text('LIB-AN-ACTION-CANCEL') ?>
						</a>
						<button type="submit" class="btn btn-primary">
						<?= @text('LIB-AN-ACTION-SAVE') ?>
						</button>
				</div>
		</fieldset>
</form>
