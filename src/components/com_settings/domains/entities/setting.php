<?php

/**
 * Setting Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntitySetting extends KObject
{
    /**
    * @param entity atributes
    */
    protected $attributes;

    /**
    * @param path to the configuration file
    */
    protected $config_file_path;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {

        $this->attributes = array(

              // site
              'sitename' => 'Anahita',
              'debug' => 0,
              'sef_rewrite' => 0,
              'secret' => '',
              'error_reporting' => 0,
              'log_path' => '',
              'tmp_path' => '',
              'live_site' => 'https://',
              'template' => 'shiraz',
              'language' => 'en-GB',

              // caching
              'caching' => 0,
              'cachetime' => 1440,
              'cache_handler' => 'file',

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

              //smtp mail
              'smtpauth' => 0,
              'smtpsecure' => '',
              'smtpport' => '',
              'smtpuser' => '',
              'smtppass' => '',
              'smtphost' => '',

              //session
              'session_handler' => 'database',
              'lifetime' => 1440
            );

            $this->config_file_path = JPATH_CONFIGURATION.DS.'configuration.php';
    }

    /**
     * Load system setting attributes
     *
     * @param array $properties An array of properties.
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function load()
    {
        $setting = new JConfig();

        foreach($this->attributes as $key => $value) {
          $this->attributes[$key] = $setting->$key;
        }

        return $this;
    }

    /**
     * Save system setting attributes
     *
     * @return ComSettingsDomainEntitySetting entity object
     */
    public function save()
    {
        if(file_exists($this->config_file_path)){

            chmod($this->config_file_path, 0644);

            $content = "<?php\nclass JConfig{\n";

            foreach($this->attributes as $key=>$value) {
              if(!is_array($value)){
                $content .= "    var \$$key = '$value';\n";
              }
            }

            $content .= "}\n";

            try {
              file_put_contents($this->config_file_path, $content);
            } catch (Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }

            chmod($this->config_file_path, 0444);
        }

        return $this;
    }

    /**
    * method to set an array of data to attributes
    *
    *  @param array of key => value data
    *
    *  @return ComSettingsDomainEntitySetting object
    */
    public function setData(array $data)
    {
        $meta = $data['meta'];

        foreach ($meta as $key => $value) {
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
        return $this->attributes[$name];
    }

    /**
    *  Magic function for setting an attribute
    *
    *  @param string attribute name
    *  @param attribute value
    *
    *  @return ComSettingsDomainEntitySetting object
    */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->attributes)) {

            $strings = array(
              'sitename',
              'log_path',
              'tmp_path',
              'fromname',
              'sendmail',
              'smtpport',
              'smtpuser',
              'smtphost',
              'db',
              'user',
              'host'
            );

            $integers = array(
              'error_reporting',
              'sef_rewrite',
              'debug',
              'caching',
              'cachetime',
              'lifetime',
              'smtpauth'
            );

            $cmds = array(
              'cache_handler',
              'session_handler',
              'mailer',
              'smtpsecure',
              'template',
              'language'
            );

            $emails = array(
              'mailfrom'
            );

            if(in_array($name, $strings)){
              $value = $this->getService('koowa:filter.string')->sanitize($value);
            }

            if(in_array($name, $integers)){
              $value = $this->getService('koowa:filter.int')->sanitize($value);
            }

            if(in_array($name, $cmds)){
              $value = $this->getService('koowa:filter.cmd')->sanitize($value);
            }

            if(in_array($name, $emails)){
              $value = $this->getService('koowa:filter.email')->sanitize($value);
            }

            $this->attributes[$name] = $value;
        }

        return $this;
    }
}
