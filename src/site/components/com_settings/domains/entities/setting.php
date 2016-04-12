<?php

/**
 * Default Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntitySetting extends AnDomainEntityDefault
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
          'attributes' => array(

              // site
              'sitename',
              'debug' => array('default' => 0),
              'sef_rewrite' => array('default' => 0),
              'secret',
              'error_reporting' => array('default' => 0),
              'log_path',
              'tmp_path',
              'live_site',

              // caching
              'caching',
              'cachetime',
              'cache_handler' => array('default' => 'file'),
              'memcache_settings',

              // database
              'dbtype' => array('default' => 'mysqli'),
              'host',
              'user',
              'password',
              'db',
              'dbprefix' => array('default' => 'an_'),

              // mailer
              'mailer',
              'mailfrom',
              'fromname',
              'sendmail' => array('default' => '/usr/sbin/sendmail'),

              //smtp mail
              'smtpauth',
              'smtpsecure',
              'smtpport',
              'smtpuser',
              'smtppass',
              'smtphost',

              //session
              'session_handler' => array('default' => 'database'),
              'lifetime' => array('default' => '60'),
           ),
        ));

        parent::_initialize($config);
    }

    /**
     * ReLoad the entity properties from storage. Overriding any changes.
     *
     * @param array $properties An array of properties.
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function load($properties = array())
    {
        return $this;
    }

    /**
     * Forwards the call to the space commit entities.
     *
     * @param mixed &$failed Return the failed set
     *
     * @return bool
     */
    public function save(&$failed = null)
    {

    }
}
