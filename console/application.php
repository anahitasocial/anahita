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

    public function __construct($src, $site)
    {
        $this->src  = $src;
        $this->site = $site;

        parent::__construct($site);

        /*
        $this->addCommands(array(new \Console\Command\ComponentsMigrateUp()));
        $this->addCommands(array(new \Console\Command\ComponentsMigrateDown()));
        $this->addCommands(array(new \Console\Command\ComponentsMigrateVersion()));
        */
    }
    
    public function load()
    {        
        if ( !defined('JPATH_BASE') )
        {
            define('JPATH_BASE', $this->getSitePath().'/administrator');
            require_once ( JPATH_BASE.'/includes/framework.php' );
            $_SERVER['HTTP_HOST'] = '';
            \KService::get('com://admin/application.dispatcher')->load();            
        }                
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