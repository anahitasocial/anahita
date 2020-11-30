<? defined('ANAHITA') or die; ?>

<form action="<?= @route() ?>" method="post" class="an-entity">
  <input type="hidden" name="action" value="edit" />

    <fieldset>
        <legend><?= @text('COM-SETTINGS-CONFIGS-SERVER') ?></legend>

        <? //site name ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-CONFIGS-SITENAME'),
          'name' => 'meta[sitename]',
          'value' => $config->sitename,
          'id' => 'setting-sitename',
        )) ?>

        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-CONFIGS-LIVE-SITE'),
          'name' => 'meta[live_site]',
          'value' => $config->live_site,
          'id' => 'setting-live_site',
        )) ?>

        <? //template ?>
        <?= @helper('ui.templates', array(
          'label' => @text('COM-SETTINGS-CONFIGS-TEMPLATE'),
          'name' => 'meta[template]',
          'selected' => $config->template,
          'id' => 'setting-template',
        )) ?>

        <? //language ?>
        <?= @helper('ui.languages', array(
          'label' => @text('COM-SETTINGS-CONFIGS-LANGUAGE'),
          'name' => 'meta[language]',
          'selected' => $config->language,
          'id' => 'setting-language',
        )) ?>

        <? //log path ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-CONFIGS-LOG-PATH'),
          'name' => 'meta[log_path]',
          'value' => $config->log_path,
          'id' => 'setting-log_path',
        )) ?>

        <? //tmp path ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-CONFIGS-TMP-PATH'),
          'name' => 'meta[tmp_path]',
          'value' => $config->tmp_path,
          'id' => 'setting-tmp_path',
        )) ?>

        <? //Sectert Word ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-CONFIGS-SECRET'),
          'name' => 'meta[secret]',
          'value' => $config->secret,
          'id' => 'setting-secret',
          'disabled' => true
        )) ?>

        <? //Error Reportin ?>
        <?
          $options_error_reporting = array(
            0 => array(
              'name' => @text('COM-SETTINGS-CONFIGS-ERROR-REPORTING-OPTION-DEFAULT'),
              'value' => -1
            ),
            1 => array(
              'name' => @text('COM-SETTINGS-CONFIGS-ERROR-REPORTING-OPTION-NONE'),
              'value' => 0
            ),
            2 => array(
              'name' => @text('COM-SETTINGS-CONFIGS-ERROR-REPORTING-OPTION-SIMPLE'),
              'value' => 7
            ),
            3 => array(
              'name' => @text('COM-SETTINGS-CONFIGS-ERROR-REPORTING-OPTION-MAXIMUM'),
              'value' => 30719
            ),
          );
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-CONFIGS-ERROR-REPORTING'),
          'name' => 'meta[error_reporting]',
          'selected' => (int) $config->error_reporting,
          'id' => 'setting-error_reporting',
          'options' => $options_error_reporting,
        )) ?>

        <? //SEF Rewrite ?>
        <?
          $options_sef_rewrite = array(
              array('name' => @text('LIB-AN-YES'), 'value' => 1),
              array('name' => @text('LIB-AN-NO'), 'value' => 0),
          );
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-CONFIGS-SEF-REWRITE'),
          'name' => 'meta[sef_rewrite]',
          'selected' => (int) $config->sef_rewrite,
          'id' => 'setting-sef_rewrite',
          'options' => $options_sef_rewrite,
        )) ?>

        <? //Debug Setting ?>
        <?
          $options_debug = array(
              array('name' => @text('LIB-AN-YES'), 'value' => 1),
              array('name' => @text('LIB-AN-NO'), 'value' => 0),
          );
        ?>
        <?= @helper('ui.formfield_select', array(
          'label' => @text('COM-SETTINGS-CONFIGS-DEBUG'),
          'name' => 'meta[debug]',
          'selected' => (int) $config->debug,
          'id' => 'setting-debug',
          'options' => $options_debug,
        )) ?>

    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-CONFIGS-DATABASE-SETTINGS') ?></legend>

      <? //database type ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-DBTYPE'),
        'name' => 'meta[dbtype]',
        'value' => $config->dbtype,
        'id' => 'setting-dbtype',
        'disabled' => true
      )) ?>

      <? //database hostname ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-HOST'),
        'name' => 'meta[host]',
        'value' => $config->host,
        'id' => 'setting-host',
      )) ?>

      <? //database username ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-USER'),
        'name' => 'meta[user]',
        'value' => $config->user,
        'id' => 'setting-user',
      )) ?>

      <? //database name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-DB'),
        'name' => 'meta[db]',
        'value' => $config->db,
        'id' => 'setting-db',
      )) ?>

      <? //database table prefix ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-DBPREFIX'),
        'name' => 'meta[dbprefix]',
        'value' => $config->dbprefix,
        'id' => 'setting-dbprefix',
      )) ?>
    </fieldset>
    
    <fieldset>
      <legend><?= @text('COM-SETTINGS-CONFIGS-CORS-SETTINGS') ?></legend>
      
      <? //CORS Enabled ?>
      <?
        $options_cors_enabled = array(
            array('name' => @text('LIB-AN-YES'), 'value' => 1),
            array('name' => @text('LIB-AN-NO'), 'value' => 0),
        );
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-CONFIGS-CORS-ENABLED'),
        'name' => 'meta[cors_enabled]',
        'selected' => (int) $config->cors_enabled,
        'id' => 'setting-cors_enabled',
        'options' => $options_cors_enabled,
      )) ?>
      
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-CORS-ORIGIN'),
        'name' => 'meta[cors_origin]',
        'value' => $config->cors_origin,
        'id' => 'setting-cors-origin',
        'required' => false,
      )) ?>
      
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-CORS-METHODS'),
        'name' => 'meta[cors_methods]',
        'value' => $config->cors_methods,
        'id' => 'setting-cors-methods',
        'required' => false,
      )) ?>
      
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-CORS-HEADERS'),
        'name' => 'meta[cors_headers]',
        'value' => $config->cors_headers,
        'id' => 'setting-cors-headers',
        'required' => false,
      )) ?>
      
      <?
        $options_cors_credentials = array(
            array('name' => @text('LIB-AN-YES'), 'value' => 1),
            array('name' => @text('LIB-AN-NO'), 'value' => 0),
        );
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-CONFIGS-CORS-CREDENTIALS'),
        'name' => 'meta[cors_credentials]',
        'selected' => (int) $config->cors_credentials,
        'id' => 'setting-cors_credentials',
        'options' => $options_cors_credentials,
      )) ?>
      
    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-CONFIGS-MAIL-SETTINGS') ?></legend>

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
        'label' => @text('COM-SETTINGS-CONFIGS-MAILER'),
        'name' => 'meta[mailer]',
        'selected' => $config->mailer,
        'id' => 'setting-mailer',
        'options' => $options_mailer,
      )) ?>

      <? //mail from ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-MAILFROM'),
        'name' => 'meta[mailfrom]',
        'value' => $config->mailfrom,
        'id' => 'setting-mailfrom',
      )) ?>

      <? //from name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-FROMNAME'),
        'name' => 'meta[fromname]',
        'value' => $config->fromname,
        'id' => 'setting-fromname',
      )) ?>

      <? //sendmail path ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SENDMAIL'),
        'name' => 'meta[sendmail]',
        'value' => $config->sendmail,
        'id' => 'setting-sendmail',
      )) ?>

      <? //smtp auth ?>
      <?
        $options_smtpauth = array(
            array('name' => @text('LIB-AN-YES'), 'value' => 1),
            array('name' => @text('LIB-AN-NO'), 'value' => 0),
        );
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPAUTH'),
        'name' => 'meta[smtpauth]',
        'selected' => (int) $config->smtpauth,
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
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPSECURE'),
        'name' => 'meta[smtpsecure]',
        'selected' => $config->smtpsecure,
        'id' => 'setting-smtpsecure',
        'options' => $options_smtpsecure,
      )) ?>

      <? //smtp port ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPPORT'),
        'name' => 'meta[smtpport]',
        'value' => $config->smtpport,
        'id' => 'setting-smtpport',
        'required' => false,
      )) ?>

      <? //smtp user ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPUSER'),
        'name' => 'meta[smtpuser]',
        'value' => $config->smtpuser,
        'id' => 'setting-smtpuser',
        'required' => false,
      )) ?>

      <? //smtp pass ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPPASS'),
        'name' => 'meta[smtppass]',
        'value' => $config->smtppass,
        'id' => 'setting-smtppass',
        'type' => 'password',
        'required' => false,
      )) ?>

      <? //smtp host ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-CONFIGS-SMTPHOST'),
        'name' => 'meta[smtphost]',
        'value' => $config->smtphost,
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
