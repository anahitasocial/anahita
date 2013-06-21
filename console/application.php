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
    protected $config;
    protected $config_path;
    protected $env;
    
    public function __construct($src, $site, $package_paths = array(), $config_path)
    {
        $this->src  = $src;
        $this->site = $site;
        settype($package_paths, 'array');
        $this->package_paths   = $package_paths;
        $this->package_paths[] = $src.'/src/packages';
        $config = array();
        if ( file_exists($config_path) ) {
            $config = Yaml::parse($config_path);            
        }
        $this->config      = new \KConfig($config);
        $this->config_path = $config_path;
        $this->env         = 'development';
        parent::__construct($site);
    }
    
    public function getConfig($env = null)
    {
         if ( !$env ) {
             $env = $this->env;
         }
         $config = \KConfig::unbox($this->config->$env);
         settype($config, 'array');
         return new \KConfig($config);
    }
    
    public function addConfig($env = null, $config = array())
    {
        if ( !$env ) {
            $env = $this->env;
        }        
        $this->config->append(array(
                $env => $config
        ));        
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
        file_put_contents($this->config_path, Yaml::dump($this->config->toArray(), 10));
    }
}

?>