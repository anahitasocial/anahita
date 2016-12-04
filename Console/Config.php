<?php

namespace Console;

/**
 * Config class.
 *
 * Provides a way to manipulate the site configuration file
 */
class Config
{
    /**
     * Site path
     *
     * @var string
     */
    protected $_site_path;

    /**
     * Configuration key map
     *
     * @var array
     */
    protected $_key_map = array(
        'database_type' => 'dbtype',
        'database_host' => 'host',
        'database_user' => 'user',
        'database_password' => 'password',
        'database_name' => 'db',
        'database_prefix' => 'dbprefix',
        'enable_debug' => 'debug',
        'url_rewrite' => 'sef_rewrite',
        'secret',
        'error_reporting'
    );

    /**
     * Configuration data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Configuration file
     *
     * @var string
     */
    protected $_configuration_file;

    /**
     * Creates a configuration from a configuration.php file
     *
     * @param string $site_path
     */
    public function __construct($site_path)
    {
        $this->_site_path = $site_path;
        $map = array();

        foreach($this->_key_map as $key => $value) {

            if ( is_numeric($key) ) {
                $key = $value;
            }

            $map[$key] = $value;
        }

        $this->_key_map = $map;
        $this->_data = array(
            'mailer' => 'mail',
            'mailfrom' => 'noreply@example.com',
            'fromname' => 'Anahita Website',
            'sendmail' => '/usr/sbin/sendmail',
            'smtpauth' => '0',
            'smtpuser' => '',
            'smtppass' => '',
            'smtphost' => 'localhost',
            'log_path' => $site_path.'/log',
            'tmp_path' => $site_path.'/tmp',
            'sitename' => 'Anahita',
            'template' => 'shiraz'
        );

        $this->set(array(
           'secret' => '',
           'enable_debug' => 0,
           'error_reporting' => 0,
           'url_rewrite' => 0,
           'live_site' => 'example.com'
        ));

        $this->_configuration_file = $site_path.'/configuration.php';

        if (file_exists($this->_configuration_file)) {

            $classname = 'AnConfig'.md5(uniqid());
            $content = file_get_contents($this->_configuration_file);

            //search for the legacy JConfig first
            if (strrpos($content, 'JConfig')) {
                $content = str_replace('JConfig', $classname, $content);
            } else {
                $content = str_replace('AnConfig', $classname, $content);
            }

            $content = str_replace(array('<?php',''), '', $content);
            $classname = '\\'.$classname;
            $return = @eval($content);

            if (class_exists($classname)) {
                $config = new $classname;
                $this->_data = array_merge($this->_data, get_object_vars($config));
            }
        }

        $this->database_type = 'mysqli';
    }

    /**
     * Check if the configuation file exist
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return file_exists($this->_configuration_file);
    }

    /**
     * Sets the keys that make the debug on for a site
     */
    public function enableDebug()
    {
        $this->set(array(
            'error_reporting' => E_ALL,
            'enable_debug' => 1,
        ));
    }

    /**
     * Disable debug
     */
    public function disableDebug()
    {
        $this->set(array(
            'error_reporting' => 0,
            'enable_debug' => 0,
        ));
    }

    /**
     * Set a configuration key/value
     *
     * @param string|array $key
     * @param string $value
     *
     * @return void.
     */
    public function set($key ,$value = null)
    {
        if (is_array($key)) {

            foreach($key as $k => $v) {
                $this->$k = $v;
            }

        } else {
            $this->$key = $value;
        }
    }

    /**
     * Return a configuraiton value
     *
     * @param string $key
     *
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($key)
    {
        if (isset($this->_key_map[$key])) {
            $key = $this->_key_map[$key];
        }

        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Set a configuration value. For setting array. We can use [val1,val2]
     *
     * @param string $key
     * @param string $value
     */
    public function __set($key , $value)
    {
        $matches = array();

        if (preg_match('/^\[(.*?)\]$/', $value, $matches)) {
            $value = explode(',', $matches[1]);
        }

        if (isset($this->_key_map[$key])) {
            $key = $this->_key_map[$key];
        }

        if ($key == 'dbprefix') {
            $value = str_replace('_', '', $value).'_';
        }

        $this->_data[$key] = $value;
    }

    /**
     * Set configuration database info
     *
     * @param array $data
     *
     * @return void
     */
    public function setDatabaseInfo($data)
    {
        $data['host'] = $data['host'].':'.$data['port'];

        unset($data['port']);

        $keys = array_map(function($key) {
            return 'database_'.$key;
        }, array_keys($data));

        $data = array_combine($keys, array_values($data));

        $this->set($data);
    }

    /**
     * Get the database info
     *
     * @return array
     */
    public function getDatabaseInfo()
    {
        $parts = explode(':', $this->database_host);

        $info = array(
            'host' => $parts[0],
            'port' => isset($parts[1]) ? $parts[1] : '3306',
            'user' => $this->database_user,
            'password' => $this->database_password,
            'name' => $this->database_name,
            'prefix' => $this->database_prefix
        );

        return $info;
    }

    /**
     * Retunr the data
     *
     * @return arary
     */
    public function toData()
    {
         return $this->_data;
    }

    /**
     * Save the configuration into the file
     *
     * @return string
     */
    public function save()
    {
        $data = $this->toData();

        if (file_exists($this->_configuration_file) && !is_writable($this->_configuration_file)) {
            chmod($this->_configuration_file, 0644);
        }

        $file = new \SplFileObject($this->_configuration_file, 'w');
        $file->fwrite("<?php\n");
        $file->fwrite("class AnConfig {\n\n");

        $print_array = function($array) use (&$print_array) {

            if (is_array($array)) {

                $values = array();
                $hash   = !is_numeric(key($array));

                foreach ($array as $key => $value) {

                    if ( !is_numeric($key) ) {
                        $key = "'".addslashes($key)."'";
                    }

                    if ( !is_numeric($value) ) {
                        $value = "'".addslashes($value)."'";
                    }

                    $values[] = $hash ? "$key=>$value" : $value;
                }

                return 'array('.implode(',', $values).')';
            }
        };

        $write = function($data) use($file, $print_array) {

            foreach($data as $key => $value) {

                if (is_array($value)) {
                    $value = $print_array($value);
                } elseif ( !is_numeric($value) ) {
                    $value = "'".addslashes($value)."'";
                }

                $file->fwrite("   var \$$key = $value;\n");
            }
        };

        $write_group = function($keys, $comment = null) use (&$data, $file, $write) {

            $values = array();

            foreach ($keys as $key) {

                if (isset($data[$key])) {
                    $values[$key] = $data[$key];
                    unset($data[$key]);
                }
            }

            if (!empty($values)) {

                if (!empty($comment)) {
                    $file->fwrite("   /*$comment*/\n");
                }

                $write($values);
                $file->fwrite("\n");
            }
        };

        $write_group(array('sitename'), 'Site Settings');
        $write_group(array('dbtype', 'host', 'user', 'password', 'db', 'dbprefix'), 'Database Settings');
        $write_group(array('sef_rewrite', 'live_site', 'secret', 'error_reporting', 'tmp_path', 'log_path', 'force_ssl'), 'Server Settings');
        $write_group(array('mailer', 'mailfrom', 'fromname', 'sendmail', 'smtpauth', 'smtpuser', 'smtppass', 'smtphost'), 'Mail Settings');
        $write_group(array('debug'), 'Debug Settings');
        $write_group(array_keys($data), 'Other configurations');
        $file->fwrite("}");

        $this->_clearCache();
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
