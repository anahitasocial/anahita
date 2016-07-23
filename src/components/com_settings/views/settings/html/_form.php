<? defined('KOOWA') or die; ?>

<form action="<?= @route() ?>" method="post" class="an-entity">
  <input type="hidden" name="action" value="edit" />

    <fieldset>
        <legend><?= @text('COM-SETTINGS-SYSTEM-SERVER') ?></legend>

        <? //site name ?>
        <?= @helper('ui.formfield_text', array(
          'label' => @text('COM-SETTINGS-SYSTEM-SITENAME'),
          'name' => 'sitename',
          'value' => $setting->sitename,
          'id' => 'setting-sitename',
        )) ?>

        <? //template ?>
        <?= @helper('ui.templates', array(
          'label' => @text('COM-SETTINGS-SYSTEM-TEMPLATE'),
          'name' => 'template',
          'selected' => $setting->template,
          'id' => 'setting-template',
        )) ?>

        <? //language ?>
        <?= @helper('ui.languages', array(
          'label' => @text('COM-SETTINGS-SYSTEM-LANGUAGE'),
          'name' => 'language',
          'selected' => $setting->language,
          'id' => 'setting-language',
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
          'name' => 'error_reporting',
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
          'name' => 'sef_rewrite',
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
          'name' => 'debug',
          'selected' => (int) $setting->debug,
          'id' => 'setting-debug',
          'options' => $options_debug,
        )) ?>

    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-SYSTEM-CACHE-SETTINGS') ?></legend>

      <? //Cache ?>
      <?
        $options_caching = array();
        $options_caching[] = array('name' => @text('LIB-AN-YES'), 'value' => 1);
        $options_caching[] = array('name' => @text('LIB-AN-NO'), 'value' => 0);
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
        'value' => (int) $setting->cachetime,
        'id' => 'setting-cachetime',
        'class' => 'span1',
        'placeholder' => @text('LIB-AN-TIME-MINUTES'),
        'pattern' => '\d*',
        'maxlength' => 5
      )) ?>

      <? //cache handler ?>
      <?
        jimport('joomla.cache.cache');
    	$stores = JCache::getStores();
    	$options_cache_handler = array();
        foreach($stores as $store) {
    	    $options_cache_handler[] = array(
                'name' => @text(ucfirst($store)),
                'value' => $store
            );
    	}
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-SYSTEM-CACHE-HANDLER'),
        'name' => 'cache_handler',
        'selected' => $setting->cache_handler,
        'id' => 'setting-cache-handler',
        'options' => $options_cache_handler,
      )) ?>

    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-SYSTEM-SESSION-SETTINGS') ?></legend>

      <? //Session Lifetime ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-LIFETIME'),
        'name' => 'lifetime',
        'value' => $setting->lifetime,
        'id' => 'setting-lifetime',
        'class' => 'span1',
        'placeholder' => @text('LIB-AN-TIME-MINUTES'),
        'pattern' => '\d*',
        'maxlength' => 10
      )) ?>

      <? //session handler ?>
      <?
        $stores = @service('com:people.session')->getStores();
    	$options_session_handler = array();
    	foreach($stores as $store) {
    	    $options_session_handler[] = array(
                'name' => @text(ucfirst($store)),
                'value' => $store
            );
    	}
      ?>
      <?= @helper('ui.formfield_select', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SESSION-HANDLER'),
        'name' => 'session_handler',
        'selected' => $setting->session_handler,
        'id' => 'setting-session-handler',
        'options' => $options_session_handler,
      )) ?>

    </fieldset>

    <fieldset>
      <legend><?= @text('COM-SETTINGS-SYSTEM-DATABASE-SETTINGS') ?></legend>

      <? //database type ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DBTYPE'),
        'name' => 'dbtype',
        'value' => $setting->dbtype,
        'id' => 'setting-dbtype',
        'disabled' => true
      )) ?>

      <? //database hostname ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-HOST'),
        'name' => 'host',
        'value' => $setting->host,
        'id' => 'setting-host',
      )) ?>

      <? //database username ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-USER'),
        'name' => 'user',
        'value' => $setting->user,
        'id' => 'setting-user',
      )) ?>

      <? //database name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DB'),
        'name' => 'db',
        'value' => $setting->db,
        'id' => 'setting-db',
      )) ?>

      <? //database table prefix ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-DBPREFIX'),
        'name' => 'dbprefix',
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
        'name' => 'mailer',
        'selected' => $setting->mailer,
        'id' => 'setting-mailer',
        'options' => $options_mailer,
      )) ?>

      <? //mail from ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-MAILFROM'),
        'name' => 'mailfrom',
        'value' => $setting->mailfrom,
        'id' => 'setting-mailfrom',
      )) ?>

      <? //from name ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-FROMNAME'),
        'name' => 'fromname',
        'value' => $setting->fromname,
        'id' => 'setting-fromname',
      )) ?>

      <? //sendmail path ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SENDMAIL'),
        'name' => 'sendmail',
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
        'name' => 'smtpauth',
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
        'name' => 'smtpsecure',
        'selected' => $setting->smtpsecure,
        'id' => 'setting-smtpsecure',
        'options' => $options_smtpsecure,
      )) ?>

      <? //smtp port ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPPORT'),
        'name' => 'smtpport',
        'value' => $setting->smtpport,
        'id' => 'setting-smtpport',
        'required' => false,
      )) ?>

      <? //smtp user ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPUSER'),
        'name' => 'smtpuser',
        'value' => $setting->smtpuser,
        'id' => 'setting-smtpuser',
        'required' => false,
      )) ?>

      <? //smtp pass ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPPASS'),
        'name' => 'smtppass',
        'value' => $setting->smtppass,
        'id' => 'setting-smtppass',
        'type' => 'password',
        'required' => false,
      )) ?>

      <? //smtp host ?>
      <?= @helper('ui.formfield_text', array(
        'label' => @text('COM-SETTINGS-SYSTEM-SMTPHOST'),
        'name' => 'smtphost',
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
