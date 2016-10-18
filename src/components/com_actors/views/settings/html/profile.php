<? defined('KOOWA') or die; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PROFILE-INFORMATION') ?></h3>

<form action="<?= @route($item->getURL()) ?>" method="post" autocomplete="off">

	<fieldset>
		<legend><?= @text('COM-ACTORS-PROFILE-INFO-BASIC') ?></legend>

		<div class="control-group">
			<label class="control-label" class="control-label" for="actor-name">
				<?= @text('COM-ACTORS-NAME') ?>
			</label>
			<div class="controls">
				<input type="text" class="input-block-level" id="actor-name" size="50" maxlength="100" name="name" value="<?=$item->name?>" required />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="actor-body">
				<?= @text('COM-ACTORS-BODY') ?>
			</label>
			<div class="controls">
				<textarea class="input-block-level" id="actor-body" name="body" rows="5" cols="5"><?= $item->body?></textarea>
			</div>
		</div>
	</fieldset>

	<? foreach ($profile as $header => $fields)  : ?>
	<fieldset>
		<legend><?= @text($header) ?></legend>
		<? foreach ($fields as $label => $field) : ?>
		<div class="control-group">
			<label><?= @text($label) ?></label>
			<div class="controls">
				<? if (is_object($field)) : ?>
				<? $class = (in_array($field->name, array('textarea', 'input'))) ? 'input-block-level' : '' ?>
				<?= $field->class($class)->rows(5)->cols(5) ?>
				<? else : ?>
				<?= $field ?>
				<? endif;?>
			</div>
		</div>
		<? endforeach;?>
	</fieldset>
	<? endforeach;?>

	<div class="form-actions">
        <button type="submit" class="btn" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-SAVE'); ?>
        </button>
    </div>
</form>
