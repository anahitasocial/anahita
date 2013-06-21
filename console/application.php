<?php 

namespace Console;

require_once 'console/class.php';
require_once 'console/config.php';


use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

class Application extends \Symfony\Component\Console\Application
{
    protected $site;
    protected $src;
    protected $packages_paths;
    
    public function __construct($src, $site, $package_paths = array())
    {
        $this->src  = $src;
        $this->site = $site;
        settype($package_paths, 'array');
        $this->package_paths   = $package_paths;
        $this->package_paths[] = $src.'/src/packages';

        parent::__construct($site);
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
}

?>