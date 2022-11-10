<?php

namespace Console;

require_once 'Console/class.php';
require_once 'Console/Config.php';

require_once __DIR__.'/../src/libraries/anahita/config/interface.php';
require_once __DIR__.'/../src/libraries/anahita/config/config.php';
require_once __DIR__.'/../src/libraries/anahita/functions.php';

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\ArgvInput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Main Anahita Console Application
 *
 * Provides anahtia related methods
 *
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * Installable Anahtia extension package
     *
     * @var Extension\Packages
     */
    protected $_packages;

    /**
     * Anahita text logo
     */
    protected static $_logo = <<<LOGO
    _                _     _ _        
   / \   _ __   __ _| |__ (_) |_ __ _ 
  / _ \ | '_ \ / _` | '_ \| | __/ _` |
 / ___ \| | | | (_| | | | | | || (_| |
/_/   \_\_| |_|\__,_|_| |_|_|\__\__,_|


LOGO;

    /**
     * Slogan
     */
    protected static $_slogan = <<<LOGO
| <fg=green>Women</> <fg=white>Life</> <fg=red>Freedom</>
| <comment>We stand in solidarity with Iranian women!</comment>
| #MahsaAmini #WomenLifeFreedom #IranRevolution        


LOGO;

    /**
     * Provide task callbacks. $applicatio->registerBefore('task', function(){});
     *
     * @var array
     */
    protected $_callbacks;

    /**
     * Anahita version number
     */
    protected $_version;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_callbacks = array('before'=>array(),'after'=>array());
        $this->_packages = new Extension\Packages();
        $this->_packages->addPackageFromComposerFiles(COMPOSER_ROOT.'/composer.json');
        $this->_packages->addPackageFromComposerFiles(Extension\Helper::getComposerFiles(COMPOSER_ROOT.'/packages'));

        //@TODO very bad way of doing it.
        //should read composer.lock or installed.json file
        foreach (new \DirectoryIterator(COMPOSER_VENDOR_DIR) as $dir) {
            if ($dir->isDir() && !$dir->isDot()) {
                $this->_packages->addPackageFromComposerFiles(Extension\Helper::getComposerFiles($dir->getPathName()));
            }
        }

        $this->_packages->addPackageFromComposerFiles(Extension\Helper::getComposerFiles(ANAHITA_ROOT.'/packages'));

        parent::__construct();

        $this->loadFramework();
        require_once __DIR__.'/../src/libraries/anahita/anahita.php';
        $this->version = \Anahita::getVersion();
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        $version = sprintf("v%s\n\n", $this->version);
        return static::$_logo . $version . static::$_slogan . parent::getHelp();
    }

    /**
     * Loads the Anahita Framework
     */
    public function loadFramework()
    {
        if (!defined('ANPATH_BASE')) {
            $_composerLoader = $GLOBALS['composerLoader'];
            define('ANPATH_BASE', WWW_ROOT);
            $_SERVER['HTTP_HOST'] = '';
            require_once ( ANPATH_BASE.'/includes/framework.php' );
            \AnService::get('com://site/application.dispatcher')->load();
            global $composerLoader, $console;
            $composerLoader = $_composerLoader;
            $console = $this;
        }
    }

    /**
     * Register a task before callback
     *
     * @param string $name
     * @param mixed $callback
     *
     * @return void
     */
    public function registerBefore($name, $callback)
    {
        $this->_callbacks['before'][$name][] = $callback;
    }

    /**
     * Return if the application is initialized
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return file_exists(WWW_ROOT.'/configuration.php');
    }

    /**
     * Register a task after callback
     *
     * @param string $name
     * @param mixed $callback
     *
     * @return void
     */
    public function registerAfter($name, $callback)
    {
        $this->_callbacks['name'][$name][] = $callback;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Console\Application::doRun()
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {        
        $name = $this->getCommandName($input);
        $result = true;

        if (isset($this->_callbacks['before'][$name])){

            $callbacks = $this->_callbacks['before'][$name];

            foreach($callbacks as $callback) {

                $result = call_user_func_array($callback, array($input, $output));

                if ($result === false) {
                    break;
                }
            }
        }

        if ($result !== false) {

            parent::doRun($input, $output);

            if (isset($this->_callbacks['after'][$name])) {

                $callbacks = $this->_callbacks['after'][$name];

                foreach ($callbacks as $callback) {
                    call_user_func_array($callback, array($input, $output));
                }
            }
        }
    }

    /**
     * Return a set of extension packages
     *
     * @return Exension/Packages
     */
    public function getExtensionPackages()
    {
         return $this->_packages;
    }

    /**
     * Runs a command.
     *
     * This method provides a way to run a command during the excution of another command
     *
     * @param string $command
     *
     * @return void
     */
    public function runCommand($command)
    {
        $this->setAutoExit(false);
        $argv = explode(' ','application '.$command);
        $input = new ArgvInput($argv);
        $this->run($input);
    }
}
