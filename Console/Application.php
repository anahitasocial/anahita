<?php 

namespace Console;

require_once 'Console/class.php';
require_once 'Console/Config.php';

require_once __DIR__.'/../vendor/nooku/libraries/koowa/config/interface.php';
require_once __DIR__.'/../vendor/nooku/libraries/koowa/config/config.php';
require_once __DIR__.'/../src/anahita/libraries/anahita/functions.php';

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\ArgvInput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Application extends \Symfony\Component\Console\Application
{
    protected $site;
    protected $src;
    protected $packages_paths;    
    protected $_callbacks;
    
    public function __construct($composer_root, $anahita_root)
    {
        $this->src  = $anahita_root;
        $this->site = $composer_root.'/www';
        $this->_callbacks = array('before'=>array(),'after'=>array());
        settype($package_paths, 'array');
        $this->package_paths   = $package_paths;
        $this->package_paths['Core'] = $src.'/src/packages';
                
        parent::__construct($site);
    }
    
    
    public function getConfiguration()
    {
        
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
    
    public function runCommand($command)
    {          
        $this->setAutoExit(false);  
        $argv  = explode(' ','application '.$command);         
        $input = new ArgvInput($argv);
        $this->run($input);
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
}

?>