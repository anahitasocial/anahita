<?php

/**
 * Config Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityConfig extends AnObject
{
    /**
    * @param entity atributes
    */
    protected $_attributes;

    /**
    * @param path to the configuration file
    */
    protected $_config_file_path;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {

        $this->_attributes = array(

              // site
              'sitename' => 'Anahita',
              'live_site' => 'example.com',
              'debug' => 0,
              'sef_rewrite' => 0,
              'secret' => '',
              'error_reporting' => 0,
              'log_path' => '',
              'tmp_path' => '',
              'template' => 'shiraz',
              'language' => 'en-GB',

              // database
              'dbtype' => 'mysqli',
              'host' => '',
              'user' => '',
              'password' => '',
              'db' => '',
              'dbprefix' => '_an',

              // mailer
              'mailer' => '',
              'mailfrom' => '',
              'fromname' => '',
              'sendmail' => '/usr/sbin/sendmail',
              
              // cors
              'cors_enabled' => 0,
              'cors_origin' => 'http://localhost:3000',
              'cors_methods' => 'POST,GET,DELETE,PUT,PATCH,OPTIONS',
              'cors_headers' => 'Content-Type',
              'cors_credentials' => 1,

              //smtp mail
              'smtpauth' => 0,
              'smtpsecure' => '',
              'smtpport' => '',
              'smtpuser' => '',
              'smtppass' => '',
              'smtphost' => ''
            );

            $this->_config_file_path = ANPATH_CONFIGURATION.DS.'configuration.php';
    }

    /**
     * Load system setting attributes
     *
     * @param array $properties An array of properties.
     *
     * @return ComSettingsDomainEntityConfig entity object
     */
    public function load()
    {
        $settings = $this->getService('com:settings.config');

        foreach($this->_attributes as $key => $value) {
          $this->_attributes[$key] = $settings->$key;
        }

        return $this;
    }

    /**
     * Save system setting attributes
     *
     * @return ComSettingsDomainEntityConfig entity object
     */
    public function save()
    {
        if(file_exists($this->_config_file_path)){

            chmod($this->_config_file_path, 0644);

            $content = "<?php\nclass AnSiteConfig{\n";

            foreach($this->_attributes as $key=>$value) {
              if(!is_array($value)){
                $content .= "    var \$$key = '$value';\n";
              }
            }

            $content .= "}\n";

            try {
              file_put_contents($this->_config_file_path, $content);
            } catch (Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }

            chmod($this->_config_file_path, 0444);

            $this->_clearCache();
        }

        return $this;
    }

    /**
    * method to set an array of data to attributes
    *
    *  @param array of key => value data
    *
    *  @return ComSettingsDomainEntityConfig object
    */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
    *  Magic function for getting an attribute
    *
    *  @param string attribute name
    *  @return attribute value
    */
    public function __get($name)
    {
        return $this->_attributes[$name];
    }

    /**
    *  Magic function for setting an attribute
    *
    *  @param string attribute name
    *  @param attribute value
    *
    *  @return ComSettingsDomainEntityConfig object
    */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_attributes)) {

            $strings = array(
              'cors_origin',
              'cors_methods',
              'cors_headers',
              'sitename',
              'live_site',
              'log_path',
              'tmp_path',
              'fromname',
              'sendmail',
              'smtpport',
              'smtpuser',
              'smtphost',
              'user',
              'host',
            );

            $integers = array(
              'cors_enabled',
              'cors_credentials',
              'debug',
              'error_reporting',
              'sef_rewrite',
              'smtpauth',
            );

            $cmds = array(
              'mailer',
              'smtpsecure',
              'template',
              'language'
            );

            $emails = array(
              'mailfrom'
            );

            if(in_array($name, $strings)){
              $value = $this->getService('anahita:filter.string')->sanitize($value);
            }

            if(in_array($name, $integers)){
              $value = $this->getService('anahita:filter.int')->sanitize($value);
            }

            if(in_array($name, $cmds)){
              $value = $this->getService('anahita:filter.cmd')->sanitize($value);
            }

            if(in_array($name, $emails)){
              $value = $this->getService('anahita:filter.email')->sanitize($value);
            }

            $this->_attributes[$name] = $value;
        }

        return $this;
    }

    protected function _clearCache()
    {
        if (extension_loaded('Zend OPcache') && ini_get('opcache.enable')) {
            opcache_reset();
        }

        if (extension_loaded('apcu') && ini_get('apc.enabled')) {
            apcu_clear_cache();
        }
    }
}
