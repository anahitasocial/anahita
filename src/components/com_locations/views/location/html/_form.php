<? defined('KOOWA') or die; ?>

<? $location = empty($location) ? @service('repos:locations.location')->getEntity()->reset() : $location; ?>

<form action="<?= @route($location->getURL()) ?>" method="post">
		<fieldset>
			<legend><?= ($location->persisted()) ? @text('COM-LOCATIONS-FORM-EDIT') : @text('COM-LOCATIONS-FORM-NEW') ?></legend>

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
						<textarea maxlength="1000" id="entity-description" class="input-block-level" name="body" rows="5"><?= $location->body?></textarea>
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="location-address">
						<?= @text('COM-LOCATIONS-LOCATION-ADDRESS') ?>
						</label>
						<div class="controls">
						<input class="input-block-level" id="location-address" size="30" maxlength="100" name="address" value="<?= $location->address ?>" type="text" />
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="location-city">
						<?= @text('COM-LOCATIONS-LOCATION-CITY') ?>
						</label>
						<div class="controls">
						<input required class="input-block-level" id="location-city" size="30" maxlength="100" name="city" value="<?= $location->city ?>" type="text" />
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="location-state-province">
						<?= @text('COM-LOCATIONS-LOCATION-STATE-PROVINCE') ?>
						</label>
						<div class="controls">
						<input class="input-block-level" id="location-state-province" size="30" maxlength="100" name="state_province" value="<?= $location->state_province ?>" type="text" />
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="location-province">
						<?= @text('COM-LOCATIONS-LOCATION-COUNTRY') ?>
						</label>
						<div class="controls">
						<?= @helper('selector.country', array(
									'name' => 'country',
									'id' => 'select-country',
									'required' => 'required',
									'selected' => $location->country
								))
						?>
						</div>
				</div>

				<div class="control-group">
						<label class="label-group"  for="location-postal-code">
						<?= @text('COM-LOCATIONS-LOCATION-POSTAL-CODE') ?>
						</label>
						<div class="controls">
						<input class="input-block-level" id="location-postal-code" size="30" maxlength="10" name="postalcode" value="<?= $location->postalcode ?>" type="text" />
						</div>
				</div>

				<div class="form-actions">
						<a href="<?= @route('view=locations') ?>" class="btn">
						<?= @text('LIB-AN-ACTION-CANCEL') ?>
						</a>
						<button type="submit" class="btn btn-primary">
						<?= @text('LIB-AN-ACTION-SAVE') ?>
						</button>
				</div>
		</fieldset>
</form>
