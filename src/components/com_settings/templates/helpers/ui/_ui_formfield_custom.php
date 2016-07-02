<? defined('KOOWA') or die; ?>

<div class="control-group">
  <label class="control-label" for="<?= $id ?>">
    <?= $label ?>
  </label>

  <div class="controls">
    <?= @helper($identifier, array(
      'id' => $id,
      'class' => $class,
      'name' => $name,
      'value' => $value,
      'placeholder' => $placeholder,
    )) ?>
  </div>
</div>
