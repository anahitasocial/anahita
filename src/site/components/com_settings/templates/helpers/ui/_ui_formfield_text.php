<? defined('KOOWA') or die; ?>

<div class="control-group">
  <label class="control-label" for="<?= $id ?>">
    <?= $label ?>
  </label>

  <div class="controls">
    <input
      required
      name="<?= $name ?>"
      id="<?= $id ?>"
      class="<?= $class ?>"
      value="<?= $value ?>"
      maxlength="<?= $maxlength ?>"
      type="text"
      placeholder="<?= $placeholder ?>"
      <?= ($disabled) ? 'disabled' : '' ?>
      pattern="<?= $pattern ?>"
    />
  </div>
</div>
