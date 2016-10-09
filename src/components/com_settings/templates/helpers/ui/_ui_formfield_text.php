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
      value="<?= @escape($value) ?>"
      maxlength="<?= $maxlength ?>"
      type="<?= $type ?>"
      placeholder="<?= $placeholder ?>"
      <? if (!empty($pattern)) : ?>
      pattern="<?= $pattern ?>"
      <? endif; ?>
      <?= ($disabled) ? 'disabled' : '' ?>
      <?= ($required) ? 'required' : '' ?>
    />
    <? if( $description ) : ?>
    <span class="help-inline"><?= @escape($description) ?></span>
    <? endif; ?>
  </div>
</div>
