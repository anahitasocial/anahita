<? defined('KOOWA') or die; ?>

<div class="control-group">
  <label class="control-label" for="<?= $id ?>">
    <?= $label ?>
  </label>

  <div class="controls">
    <input
      name="<?= $name ?>"
      id="<?= $id ?>"
      class="<?= $class ?>"
      value="<?= $value ?>"
      maxlength="<?= $maxlength ?>"
      type="<?= $type ?>"
      placeholder="<?= $placeholder ?>"
      pattern="<?= $pattern ?>"
      <?= ($disabled) ? 'disabled' : '' ?>
      <?= ($required) ? 'required' : '' ?>
    />
  </div>
</div>
