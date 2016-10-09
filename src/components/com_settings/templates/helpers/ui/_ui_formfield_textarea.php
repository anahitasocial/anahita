<? defined('KOOWA') or die; ?>

<div class="control-group">
  <label class="control-label" for="<?= $id ?>">
    <?= $label ?>
  </label>

  <div class="controls">
    <? if( $description ) : ?>
    <span class="help-inline"><?= @escape($description) ?></span>
    <? endif; ?>
    <textarea
      name="<?= $name ?>"
      id="<?= $id ?>"
      class="<?= $class ?>"
      placeholder="<?= $placeholder ?>"
      maxlength="<?= $maxlength ?>"
      cols="<?= $cols ?>"
      rows="<?= $rows ?>"
      <?= ($disabled) ? 'disabled' : '' ?>
      <?= ($required) ? 'required' : '' ?>
    ><?= @escape(str_replace('\n', "\n", $value)) ?></textarea>
  </div>
</div>
