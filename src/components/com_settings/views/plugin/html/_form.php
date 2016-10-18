<? defined('KOOWA') or die; ?>

<form action="<?= @route($item->getURL()) ?>" method="post" class="an-entity">

  <fieldset>
      <legend><?= @text('COM-SETTINGS-PLUGIN-DETAILS') ?></legend>
      <dl>
        <dt><?= @text('ID') ?></dt>
        <dd><?= @escape($item->id) ?></dd>
        <dt><?= @text('LIB-AN-ENTITY-NAME') ?></dt>
        <dd><?= @escape($item->name) ?></dd>
      </dl>

      <?
        $options_enabled = array();
        $options_enabled[] = array('name' => @text('LIB-AN-YES'), 'value' => 1);
        $options_enabled[] = array('name' => @text('LIB-AN-NO'), 'value' => 0);
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-PLUGIN-ENABLED'),
        'name' => 'enabled',
        'selected' => (int) $plugin->enabled,
        'id' => 'plugin-enabled',
        'options' => $options_enabled
      )) ?>
  </fieldset>

  <h3><?= @text('COM-SETTINGS-APP-CONFIGURATIONS') ?></h3>

  <fieldset>
      <?= @helper('ui.params', array(
        'entity' => $item,
        'type' => 'plugin'
      )) ?>
  </fieldset>

  <div class="form-actions">
    <a href="<?= @route('view=plugins') ?>" class="btn">
      <?= @text('LIB-AN-ACTION-CANCEL') ?>
    </a>

    <button type="submit" class="btn btn-primary">
      <?= @text('LIB-AN-ACTION-UPDATE') ?>
    </button>
  </div>
</form>
