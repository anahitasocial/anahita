<? defined('KOOWA') or die; ?>

<form action="<?= @route() ?>" method="post" class="an-entity">

    <h2><?= @text('COM-SETTINGS-SYSTEM-HEADER') ?></h2>

    <? //site name ?>
    <?= @helper('ui.formfield_text', array(
      'label' => @text('COM-SETTINGS-SYSTEM-SITENAME'),
      'name' => 'sitename',
      'value' => $setting->sitename,
      'id' => 'setting-sitename',
    )) ?>

    <? //log path ?>
    <?= @helper('ui.formfield_text', array(
      'label' => @text('COM-SETTINGS-SYSTEM-LOG-PATH'),
      'name' => 'log_path',
      'value' => $setting->log_path,
      'id' => 'setting-log_path',
    )) ?>

    <? //tmp path ?>
    <?= @helper('ui.formfield_text', array(
      'label' => @text('COM-SETTINGS-SYSTEM-TMP-PATH'),
      'name' => 'tmp_path',
      'value' => $setting->tmp_path,
      'id' => 'setting-tmp_path',
    )) ?>

    <? //Sectert Word ?>
    <?= @helper('ui.formfield_text', array(
      'label' => @text('COM-SETTINGS-SYSTEM-SECRET'),
      'name' => 'secret',
      'value' => $setting->secret,
      'id' => 'setting-secret',
      'disabled' => true
    )) ?>

    <? //Error Reportin ?>
    <?
      $options_error_reporting = array(
        0 => array(
          'name' => 'COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-DEFAULT',
          'value' => -1
        ),
        1 => array(
          'name' => 'COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-NONE',
          'value' => 0
        ),
        2 => array(
          'name' => 'COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-SIMPLE',
          'value' => 7
        ),
        3 => array(
          'name' => 'COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-MAXIMUM',
          'value' => 30719
        ),
      );
    ?>
    <?= @helper('ui.formfield_select', array(
      'label' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING'),
      'name' => 'error_reporting',
      'selected' => (int) $setting->error_reporting,
      'id' => 'setting-error_reporting',
      'options' => $options_error_reporting,
    )) ?>

    <? //SEF Rewrite ?>
    <?
      $options_sef_rewrite = array(
        0 => array(
          'name' => 'LIB-AN-YES',
          'value' => -1
        ),
        1 => array(
          'name' => 'LIB-AN-NO',
          'value' => 0
        ),
      );
    ?>
    <?= @helper('ui.formfield_select', array(
      'label' => @text('COM-SETTINGS-SYSTEM-SEF-REWRITE'),
      'name' => 'error_reporting',
      'selected' => (int) $setting->sef_rewrite,
      'id' => 'setting-sef_rewrite',
      'options' => $options_sef_rewrite,
    )) ?>

    <? //Debug Setting ?>
    <?
      $options_debug = array(
        0 => array(
          'name' => 'LIB-AN-YES',
          'value' => -1
        ),
        1 => array(
          'name' => 'LIB-AN-NO',
          'value' => 0
        ),
      );
    ?>
    <?= @helper('ui.formfield_select', array(
      'label' => @text('COM-SETTINGS-SYSTEM-DEBUG'),
      'name' => 'debug',
      'selected' => (int) $setting->debug,
      'id' => 'setting-debug',
      'options' => $options_debug,
    )) ?>

    <? //Cache ?>
    <?
      $options_caching = array(
        0 => array(
          'name' => 'LIB-AN-YES',
          'value' => -1
        ),
        1 => array(
          'name' => 'LIB-AN-NO',
          'value' => 0
        ),
      );
    ?>
    <?= @helper('ui.formfield_select', array(
      'label' => @text('COM-SETTINGS-SYSTEM-CACHING'),
      'name' => 'caching',
      'selected' => (int) $setting->caching,
      'id' => 'setting-caching',
      'options' => $options_caching,
    )) ?>

    <? //cache time ?>
    <?= @helper('ui.formfield_text', array(
      'label' => @text('COM-SETTINGS-SYSTEM-CACHETIME'),
      'name' => 'cachetime',
      'value' => $setting->cachetime,
      'id' => 'setting-cachetime',
      'class' => 'span1',
      'placeholder' => @text('LIB-AN-TIME-MINUTES'),
      'pattern' => '[0-9]{5}',
      'maxlength' => 5
    )) ?>

    <? //cache handler ?>
    
</form>
