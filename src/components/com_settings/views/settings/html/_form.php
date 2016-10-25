<? defined('KOOWA') or die; ?>

<form action="<?= @route() ?>" method="post" class="an-entity">
  <input type="hidden" name="action" value="edit" />

    <fieldset>
        <legend><?= @text('COM-SETTINGS-SYSTEM-SERVER') ?></legend>

        <? //site name ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-SITENAME'),
          'name' => 'meta[sitename]',
          'value' => $setting->sitename,
          'id' => 'setting-sitename',
        )) ?>

        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-LIVE-SITE'),
          'name' => 'meta[live_site]',
          'value' => $setting->live_site,
          'id' => 'setting-live_site',
        )) ?>

        <? //template ?>
        <?= @helper('ui.templates', array(
          'label' => @text('COM-SETTINGS-SYSTEM-TEMPLATE'),
          'name' => 'meta[template]',
          'selected' => $setting->template,
          'id' => 'setting-template',
        )) ?>

        <? //language ?>
        <?= @helper('ui.languages', array(
          'label' => @text('COM-SETTINGS-SYSTEM-LANGUAGE'),
          'name' => 'meta[language]',
          'selected' => $setting->language,
          'id' => 'setting-language',
        )) ?>

        <? //log path ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-LOG-PATH'),
          'name' => 'meta[log_path]',
          'value' => $setting->log_path,
          'id' => 'setting-log_path',
        )) ?>

        <? //tmp path ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-TMP-PATH'),
          'name' => 'meta[tmp_path]',
          'value' => $setting->tmp_path,
          'id' => 'setting-tmp_path',
        )) ?>

        <? //Sectert Word ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-SECRET'),
          'name' => 'meta[secret]',
          'value' => $setting->secret,
          'id' => 'setting-secret',
          'disabled' => true
        )) ?>

        <? //Error Reportin ?>
        <?
          $options_error_reporting = array(
            0 => array(
              'name' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-DEFAULT'),
              'value' => -1
            ),
            1 => array(
              'name' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-NONE'),
              'value' => 0
            ),
            2 => array(
              'name' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-SIMPLE'),
              'value' => 7
            ),
            3 => array(
              'name' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING-OPTION-MAXIMUM'),
              'value' => 30719
            ),
          );
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-SYSTEM-ERROR-REPORTING'),
          'name' => 'meta[error_reporting]',
          'selected' => (int) $setting->error_reporting,
          'id' => 'setting-error_reporting',
          'options' => $options_error_reporting,
        )) ?>

        <? //SEF Rewrite ?>
        <?
          $options_sef_rewrite = array();
          $options_sef_rewrite[] = array('name' => @text('LIB-AN-YES'), 'value' => 1);
          $options_sef_rewrite[] = array('name' => @text('LIB-AN-NO'), 'value' => 0);
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-SYSTEM-SEF-REWRITE'),
          'name' => 'meta[sef_rewrite]',
          'selected' => (int) $setting->sef_rewrite,
          'id' => 'setting-sef_rewrite',
          'options' => $options_sef_rewrite,
        )) ?>

        <? //Debug Setting ?>
        <?
          $options_debug = array();
          $options_debug[] = array('name' => @text('LIB-AN-YES'), 'value' => 1);
          $options_debug[] = array('name' => @text('LIB-AN-NO'), 'value' => 0);
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-SYSTEM-DEBUG'),
          'name' => 'meta[debug]',
          'selected' => (int) $setting->debug,
          'id' => 'setting-debug',
          'options' => $options_debug,
        )) ?>

    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-SYSTEM-DATABASE-SETTINGS') ?></legend>

      <? //database type ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DBTYPE'),
        'name' => 'meta[dbtype]',
        'value' => $setting->dbtype,
        'id' => 'setting-dbtype',
        'disabled' => true
      )) ?>

      <? //database hostname ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-HOST'),
        'name' => 'meta[host]',
        'value' => $setting->host,
        'id' => 'setting-host',
      )) ?>

      <? //database username ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-USER'),
        'name' => 'meta[user]',
        'value' => $setting->user,
        'id' => 'setting-user',
      )) ?>

      <? //database name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DB'),
        'name' => 'meta[db]',
        'value' => $setting->db,
        'id' => 'setting-db',
      )) ?>

      <? //database table prefix ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DBPREFIX'),
        'name' => 'meta[dbprefix]',
        'value' => $setting->dbprefix,
        'id' => 'setting-dbprefix',
      )) ?>
    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-SYSTEM-MAIL-SETTINGS') ?></legend>

      <? //Mailer ?>
      <?
        $options_mailer = array(
          0 => array(
            'name' => 'PHP Mail Function',
            'value' => 'mail'
          ),
          1 => array(
            'name' => 'Sendmail',
            'value' => 'sendmail'
          ),
          2 => array(
            'name' => 'SMTP',
            'value' => 'smtp'
          ),
        );
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-SYSTEM-MAILER'),
        'name' => 'meta[mailer]',
        'selected' => $setting->mailer,
        'id' => 'setting-mailer',
        'options' => $options_mailer,
      )) ?>

      <? //mail from ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-MAILFROM'),
        'name' => 'meta[mailfrom]',
        'value' => $setting->mailfrom,
        'id' => 'setting-mailfrom',
      )) ?>

      <? //from name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-FROMNAME'),
        'name' => 'meta[fromname]',
        'value' => $setting->fromname,
        'id' => 'setting-fromname',
      )) ?>

      <? //sendmail path ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SENDMAIL'),
        'name' => 'meta[sendmail]',
        'value' => $setting->sendmail,
        'id' => 'setting-sendmail',
      )) ?>

      <? //smtp auth ?>
      <?
        $options_smtpauth = array();
        $options_smtpauth[] = array('name' => @text('LIB-AN-YES'), 'value' => 1);
        $options_smtpauth[] = array('name' => @text('LIB-AN-NO'), 'value' => 0);
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPAUTH'),
        'name' => 'meta[smtpauth]',
        'selected' => (int) $setting->smtpauth,
        'id' => 'setting-caching',
        'options' => $options_smtpauth,
      )) ?>

      <? //SMTP Security ?>
      <?
        $options_smtpsecure = array(
          0 => array(
            'name' => @text('LIB-AN-NONE'),
            'value' => 'none'
          ),
          1 => array(
            'name' => 'SSL',
            'value' => 'ssl'
          ),
          2 => array(
            'name' => 'TLS',
            'value' => 'tls'
          ),
        );
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPSECURE'),
        'name' => 'meta[smtpsecure]',
        'selected' => $setting->smtpsecure,
        'id' => 'setting-smtpsecure',
        'options' => $options_smtpsecure,
      )) ?>

      <? //smtp port ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPPORT'),
        'name' => 'meta[smtpport]',
        'value' => $setting->smtpport,
        'id' => 'setting-smtpport',
        'required' => false,
      )) ?>

      <? //smtp user ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPUSER'),
        'name' => 'meta[smtpuser]',
        'value' => $setting->smtpuser,
        'id' => 'setting-smtpuser',
        'required' => false,
      )) ?>

      <? //smtp pass ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPPASS'),
        'name' => 'meta[smtppass]',
        'value' => $setting->smtppass,
        'id' => 'setting-smtppass',
        'type' => 'password',
        'required' => false,
      )) ?>

      <? //smtp host ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPHOST'),
        'name' => 'meta[smtphost]',
        'value' => $setting->smtphost,
        'id' => 'setting-smtphost',
        'required' => false,
      )) ?>

    </fieldset>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">
        <?= @text('LIB-AN-ACTION-UPDATE') ?>
      </button>
    </div>
</form>
