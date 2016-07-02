<? defined('KOOWA') or die; ?>

<form action="<?= @route() ?>" method="get">
<? foreach($params as $name=>$value) : ?>
<input type="hidden" name="<?= $name ?>" value="<?= $value ?>" />
<? endforeach; ?>
<div class="control-group">
  <label class="control-label" for="<?= $id ?>">
    <?= $label ?>
  </label>

  <div class="controls">
      <select name="type" data-filter="plugin-type" onChange="this.form.submit()">
      <option value=""><?= @text('COM-SETTINGS-PLUGIN-FILTER-OPTION-SHOW-ALL') ?></option>
      <? foreach($folders as $folder): ?>
      <option <?= ($folder == $selected) ? 'selected' : '' ?> value="<?= $folder ?>">
        <?= $folder ?>
      </option>
      <? endforeach; ?>
      </select>
  </div>
</div>
</form>
