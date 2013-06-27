<?php 

namespace Console;

class Config
{
    protected $_site_path;
    
    protected $_key_map = array(        
        'database_type'      => 'dbtype',
        'database_host'      => 'host',
        'database_user'      => 'user',
        'database_password'  => 'password',
        'database_name'      => 'db',
        'database_prefix'    => 'dbprefix',
        'enable_debug'       => 'debug',
        'enable_caching'     => 'caching',
        'url_rewrite'        => 'sef_rewrite',
        'cache_lifetime'     => 'cachetime',
        'session_lifetime'   => 'lifetime',
        'offline',                       
        'secret',
        'error_reporting',
        'session_handler',
        'cache_handler'
    );
    
    protected $_data = array();
    
    protected $_configuration_file;
    
    public function __construct($site_path)
    {
        $this->_site_path = $site_path;
        $map = array();
        foreach($this->_key_map as $key => $value) 
        {
            if ( is_numeric($key) ) {
                $key = $value;
            }    
            $map[$key] = $value;
        }
        $this->_key_map = $map;
        $this->_data = array(
            'debug_db'     => 0,
            'debug_lang'   => 0,
            'mailer'       => 'mail',
            'mailfrom'     => '',
            'sendmail'     => '/usr/sbin/sendmail',
            'smtpauth'     => '0',
            'smtpuser'     => '',
            'smtppass'     => '',
            'smtphost'     => 'localhost',
            'force_ssl'    => 0,
            'log_path'     => $site_path.'/log',
            'tmp_path'     => $site_path.'/tmp',
            'offline_message' => 'This site is down for maintenance.<br /> Please check back again soon.',
            'sitename'        => 'Anahita',
            'editor'          => 'tinymce'     
        );
        
        $this->set(array(
           'secret'           => '',
           'offline'          => 0,
           'enable_debug'     => 0,
           'cache_duration'   => 15,
           'session_duration' => 15,
           'error_reporting' => 0,
           'enable_caching'  => 1,
           'url_rewrite'     => 0,
           'session_handler'    => function_exists('apc_fetch') ? 'apc' : 'database',
           'cache_handler'      => function_exists('apc_fetch') ? 'apc' : 'file'
        ));
        
        $this->_configuration_file = $site_path.'/configuration.php'; 
        if ( file_exists($this->_configuration_file) ) 
        {
            $classname = 'JConfig'.md5(uniqid());            
            $content   = file_get_contents($this->_configuration_file);
            $content   = str_replace('JConfig', $classname, $content);
            $content   = str_replace(array('<?php',''), '', $content);            
            $classname = '\\'.$classname;
            $return = eval($content);  
            if ( class_exists($classname) ) 
            {
                $config = new $classname;
                $this->_data = array_merge($this->_data, get_object_vars($config));                                
            }                      
        }
        $this->database_type = 'mysqli';
    }
    
    public function enableDebug()
    {
        $this->set(array(
            'error_reporting' => E_ALL,
            'enable_debug'    => 1,
        ));    
    }
    
    public function disableDebug()
    {
        $this->set(array(
            'error_reporting' => 0,
            'enable_debug'    => 0,
        ));
    }
    
    public function set($key ,$value = null)
    {
        if ( is_array($key) ) 
        {
            foreach($key as $k => $v) {
                $this->$k = $v;
            }
        } else {
            $this->$key = $value;
        }
    }
    
    public function __get($key)
    {           
        if ( isset($this->_key_map[$key]) )
        {
            $key = $this->_key_map[$key];
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
    
    public function __set($key , $value)
    {
        if ( isset($this->_key_map[$key]) )
        {
            $key = $this->_key_map[$key];
            $this->_data[$key] = $value;
        }
    }
    
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
    
    public function getDatabaseInfo()
    {                
        $parts = explode(':', $this->database_host);
        
        return array(
             'host'      => $parts[0],
             'port'      => isset($parts[1]) ? $parts[1] : '3306', 
             'user'      => $this->database_user,
             'password'  => $this->database_password,
             'name'      => $this->database_name,
             'prefix'    => $this->database_prefix    
        );
    }
    
    public function toData()
    {
         return $this->_data;   
    }
    
    public function save()
    {
        $data   = $this->toData();
        $file   = new \SplFileObject($this->_configuration_file, 'w');
        $file->fwrite("<?php\n");
        $file->fwrite("class JConfig {\n\n");
        $write = function($data) use($file) 
        {
            foreach($data as $key => $value)
            {
                if ( !is_numeric($value) ) {
                    $value = "'".addslashes($value)."'";
                }
                $file->fwrite("   var \$$key = $value;\n");
            }
        };
        $write_group = function($keys, $comment = null)
             use ($data, $file, $write) 
        {
            if ( !empty($comment) ) {
                $file->fwrite("   /*$comment*/\n");
            }
            $values = array();
            foreach($keys as $key) {
                $values[$key] = $data[$key];
            }
            $write($values);
            $file->fwrite("\n");
        };
        $write_group(array('offline','offline_message','sitename','editor'), 'Site Settings');
        $write_group(array('dbtype','host','user','password','db','dbprefix'), 'Database Settings');
        $write_group(array('secret','error_reporting','tmp_path','log_path','force_ssl'), 'Server Settings');
        $write_group(array('lifetime','session_handler'), 'Session Settings');
        $write_group(array('mailer','mailfrom','fromname','sendmail','smtpauth','smtpuser','smtppass','smtphost'), 'Mail Settings');
        $write_group(array('caching','cachetime','cache_handler'), 'Cache Settings');
        $write_group(array('debug','debug_db','debug_lang'), 'Debug Settings');
        $write_group(array('sef_rewrite'), 'Route Settings');
        $file->fwrite("}");
    }
}
?>