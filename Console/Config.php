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

    protected $_legacy_key_map = array(
        'live_site' => 'client_domain',
        'smtpuser' => 'smtp_user',
        'smtppass' => 'smtp_pass',
        'smtphost' => 'smtp_host',
        'smtpport' => 'smtp_port',
    );

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
            // CORS Settings
            'cors_enabled' => 0,
            'cors_methods' => 'POST, GET, DELETE, PUT, PATCH, OPTIONS',
            'cors_headers' => 'Content-Type',
            'cors_credentials' => 1,
            // Mailer Settings
            'mailer' => 'mail',
            'mailfrom' => 'noreply@example.com',
            'fromname' => 'Anahita Website',
            'sendmail' => '/usr/sbin/sendmail',
            // SMTP Settings
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_host' => 'localhost',
            'smtp_secure' => 'ssl',
            'smtp_port' => 25,
            // Server Settings
            'log_path' => $site_path.'/log',
            'tmp_path' => $site_path.'/tmp',
            'sitename' => 'Anahita'
        );

        $this->set(array(
           'secret' => '',
           'debug' => 0,
           'error_reporting' => 0,
           'sef_rewrite' => 1,
           'client_domain' => 'example.com',
        ));

        $this->_configuration_file = $site_path.'/configuration.php';

        if (file_exists($this->_configuration_file)) {

            $classname = 'AnSiteConfig'.md5(uniqid());
            $content = file_get_contents($this->_configuration_file);
            $content = str_replace('AnSiteConfig', $classname, $content);

            $content = str_replace(array('<?php',''), '', $content);
            $classname = '\\'.$classname;
            $return = @eval($content);

            if (class_exists($classname)) {
                $config = new $classname;
                $config_vars = get_object_vars($config);
                
                // Replace legacy variables with the new ones
                foreach($this->_legacy_key_map as $key => $value) {
                    if (isset($config_vars[$key])) {
                        $config_vars[$value] = $config_vars[$key];
                        unset($config_vars[$key]);
                    }
                }
                
                $this->_data = array_merge($this->_data, $config_vars);
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
            'debug' => 1,
        ));
    }

    /**
     * Disable debug
     */
    public function disableDebug()
    {
        $this->set(array(
            'error_reporting' => 0,
            'debug' => 0,
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
        $file->fwrite("class AnSiteConfig {\n\n");

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

            if (! empty($values)) {
                if (! empty($comment)) {
                    $file->fwrite("   /* $comment */\n");
                }

                $write($values);
                $file->fwrite("\n");
            }
        };

        $write_group(array(
            'sitename',
            'client_domain',
            'language',
            'log_path',
            'tmp_path',
            'secret',
            'sef_rewrite',
        ), 'Server Settings');
        
        $write_group(array(
            'debug',
            'error_reporting',
        ), 'Debuging Settings');
        
        $write_group(array(
            'dbtype', 
            'host', 
            'user', 
            'password', 
            'db', 
            'dbprefix'
        ), 'Database Settings');
        
        $write_group(array(
            'cors_enabled',
            'cors_methods', 
            'cors_headers',
            'cors_credentials',
        ), 'CORS Settings');
        
        $write_group(array(
            'mailer', 
            'mailfrom', 
            'fromname', 
            'sendmail',
        ), 'Mailer Settings');
        
        $write_group(array(
            'smtp_user', 
            'smtp_pass', 
            'smtp_host',
            'smtp_secure',
            'smtp_port',
        ), 'SMTP Settings');
        
        // We really don't have other configurations.
        // $write_group(array_keys($data), 'Other configurations');
        
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
