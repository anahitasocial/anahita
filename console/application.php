<?php 

namespace Console;

require_once 'console/class.php';
require_once 'console/config.php';

require_once __DIR__.'/../vendor/nooku/libraries/koowa/config/interface.php';
require_once __DIR__.'/../vendor/nooku/libraries/koowa/config/config.php';
require_once __DIR__.'/../src/anahita/libraries/anahita/functions.php';

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Yaml\Yaml;

class Application extends \Symfony\Component\Console\Application
{
    protected $site;
    protected $src;
    protected $packages_paths;
    protected $configs;
    protected $config_dir;
    protected $env;
    protected $_callbacks;
    
    public function __construct($src, $site, $package_paths = array(), $config_dir)
    {
        $this->src  = $src;
        $this->site = $site;
        $this->_callbacks = array('before'=>array(),'after'=>array());
        settype($package_paths, 'array');
        $this->package_paths   = $package_paths;
        $this->package_paths['Core'] = $src.'/src/packages';
        $this->configs         = new \KConfig();    
        $this->config_dir      = $config_dir;
        foreach(new \DirectoryIterator($this->config_dir) as $file) 
        {
            if ( $file->getExtension() == 'yaml' ) {
                $data = Yaml::parse($file->getPathname());
                settype($data, 'array');                                
                $this->configs->
                        {strtolower($file->getBasename('.yaml'))} = $data; 
            }
        }
        $this->env             = 'development';
        parent::__construct($site);
    }
    
    public function getConfig($env = null)
    {
         if ( !$env ) {
             $env = $this->env;
         }
         $config = \KConfig::unbox($this->configs->$env);
         settype($config, 'array');
         return new \KConfig($config);
    }
    
    public function addConfig($config = array(), $env = null)
    {
        if ( !$env ) {
            $env = $this->env;
        }
        $this->configs->append(array(
            $env => $config
        ));        
    }
    
    public function setEnv($env)
    {
        $this->env = $env;
    }
    
    public function loadFramework()
    {        
        if ( !defined('JPATH_BASE') )
        {
            define('JPATH_BASE', $this->getSitePath().'/administrator');
            $_SERVER['HTTP_HOST'] = '';
            require_once ( JPATH_BASE.'/includes/framework.php' );            
            \KService::get('com://admin/application.dispatcher')->load();            
        }
    }    
    
    public function registerBefore($name, $callback)
    {
        $this->_callbacks['before'][$name][] = $callback;                    
    }
    
    public function registerAfter($name, $callback)
    {
        $this->_callbacks['name'][$name][] = $callback;
    }   

    public function doRun($input, $output)
    {
        $name      = $this->getCommandName($input);
        $result    = true;
        
        if ( isset($this->_callbacks['before'][$name]) ) 
        {
            $callbacks = $this->_callbacks['before'][$name];
            foreach($callbacks as $callback) {
                $result = call_user_func_array($callback, array($input, $output));
                if ( $result === false ) {
                    break;
                }
            }
        }
        
        if ( $result !== false ) 
        {
            parent::doRun($input, $output);
            
            if ( isset($this->_callbacks['after'][$name]) ) {
                $callbacks = $this->_callbacks['after'][$name];
                foreach($callbacks as $callback) {
                    call_user_func_array($callback, array($input, $output));
                }
            }            
        }                 
        
    }
    
    public function getPackagePaths()
    {
        return $this->package_paths;
    }

    public function getSrcPath()
    {
        return $this->src;
    }

    public function getSitePath()
    {
        return $this->site;
    }
    
    public function __destruct()
    {     
        $configs = \KConfig::unbox($this->configs);
        settype($configs, 'array');
        foreach($configs as $env => $config) {           
            file_put_contents($this->config_dir.'/'.$env.'.yaml', Yaml::dump($config, 10));
        }        
    }
}

?>