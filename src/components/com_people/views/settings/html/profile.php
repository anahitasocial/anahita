<? defined('KOOWA') or die; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PROFILE-INFORMATION') ?></h3>

<form action="<?= @route($item->getURL(false)) ?>" method="post" autocomplete="off">
	<div class="control-group">
		<label class="control-label"  for="person-given-name">
			<?= @text('COM-PEOPLE-GIVEN-NAME'); ?>
		</label>
		<div class="controls">
			<input
				required
				class="input-block-level"
				type="text"
				id="person-given-name"
				name="givenName"
				maxlength="25"
				minlength="3"
				value="<?= $item->givenName ?>"
			 />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"  for="person-family-name">
			<?= @text('COM-PEOPLE-FAMILY-NAME'); ?>
		</label>
		<div class="controls">
			<input
				required
				class="input-block-level"
				type="text"
				id="person-family-name"
				name="familyName"
				maxlength="25"
				minlength="3"
				value="<?= $item->familyName ?>"
			 />
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

    <div class="control-group">
        <label class="control-label" for="gender">
            <?= @text('COM-PEOPLE-PROFILE-GENDER') ?>
        </label>
        <div class="controls">
            <?
            $genderOptions = array(
                'neutral' => @text('COM-PEOPLE-GENDER-NEUTRAL'),
                'female' => @text('COM-PEOPLE-GENDER-FEMALE'),
				'male' => @text('COM-PEOPLE-GENDER-MALE')
			);
            ?>
            <?= @html('select', 'gender', array(
					'options' => $genderOptions,
					'selected' => $item->gender)
					)->class('input-block-level') ?>
        </div>
    </div>

    <? if ($item->authorize('changeUserType')): ?>
    <div class="control-group">
        <label class="control-label" for="person-group">
            <?= @text('COM-PEOPLE-USERTYPE'); ?>
        </label>
        <div class="controls">
            <?= @helper('usertypes', array('selected' => $item->usertype)) ?>
        </div>
    </div>
    <? endif; ?>

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
