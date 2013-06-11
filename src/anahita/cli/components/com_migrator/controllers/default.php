<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita_Dev
 * @package    Com_Migrator
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Controller
 *
 * @category   Anahita_Dev
 * @package    Com_Migrator
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMigratorControllerDefault extends LibBaseControllerResource
{
    /**
     * The folder path
     * 
     * @var string
     */ 
    protected $_path;
    
    /**
     * The migration name
     *
     * @var string
     */
    protected $_name;     
     
    /**
     * The current version
     * 
     * @var int
     */
    protected $_version;
    
    /**
     * Versioning Store
     * 
     * @var ComMigratorStoreFile
     */
    protected $_store;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        $path = $config->path;
        
        if ( !file_exists($path) ) {
            throw new KException("$path doesn't exists");
        }
        
        $this->_path = $path;
        $this->_name = preg_replace('/\..*/','',basename($path));        
        $this->_store        = ComMigratorStore::getInstance();
        $this->_version      = $this->_store->getVersion($this->_name);
        parent::__construct($config);
    }
    
    /**
     * Empty Browse Action
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */    
    protected function _actionDown(KCommandContext $context)
    {
        if ( $this->_version <= 0 ) {
            return;
        }
        $migrations = $this->_getMigrations();
        $current    = $this->_version;
        $migrated   = false;        
        foreach($migrations as $migration)
        {
            if ( $migration->version == $current )
            {
                if ( function_exists($migration->down) )
                    call_user_func($migration->down);
                $this->_version--;
                $migrated = true;
            }
            if ( $migrated )
               break;
        }
        $this->_store->setVersion($this->_name, max((int)$this->_version,0));
        $this->_store->save();
    }

    /**
     * Return the version
     *
     * @param KCommandContext $context Context parameter
     *
     * @return void
     */
    protected function _actionVersion(KCommandContext $context)
    {
        $data = $context->data;
        print($this->_name.' version:'.$this->_version."\n");
        
    }   
     
    /**
     * Empty Browse Action
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */    
    protected function _actionUp(KCommandContext $context)
    {
        $data = $context->data;
        $migrations = $this->_getMigrations();
        $current    = $this->_version;
        $output     ='';
        foreach($migrations as $migration) 
        {
            if ( $migration->version <= $current )
                continue; 
            $output .= "Migrating ".$migration->up."\n";
            if ( function_exists($migration->up) )
                call_user_func($migration->up);
           $this->_version++;
        }
        $this->_store->setVersion($this->_name, max((int)$this->_version,0));
        $this->_store->save();
        print $output."\n";
    }

    /**
     * Return an array of migraiton files order by their migration number
     *
     * @return array
     */
    protected function _getMigrations()
    {        
        $migrations  = array();        
        require_once($this->_path);
        $i    = 1;
        $name = $this->_name;           
        while(true)
        {
            $function_name_up   = $name.'_'.$i;
            $function_name_down = $name.'_'.$i.'_down';  
            if ( function_exists($function_name_up) )
            {
                $migrations[] = new KConfig(array(
                    'version' => $i,
                     'up'     => $function_name_up,
                     'down'   => $function_name_down                            
                ));
            }
            else
                break;
            $i++;
        }
        
        return $migrations;
    }
}