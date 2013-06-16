<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Migrator
 * @subpackage Migrations
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Migration
 *
 * @category   Anahita
 * @package    Com_Migrator
 * @subpackage Migrations
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComMigratorMigrationAbstract extends KObject
{
    /**
     * Array of table schmesa to dump into the install.sql
     * 
     * @var array
     */
    protected $_schemas = array();
    
    /**
     * The name of the component
     * 
     * @var string
     */
    protected $_component;
    
    /**
     * The version entity
     * 
     * @var ComMigratorDomainEntityVersion
     */   
    protected $_version;
    
    /**
     * The max version
     * 
     * @var int
     */
    protected $_max_version;
    
    /**
     * Schema SQLs
     * 
     * @var array
     */
    protected $_schema_sqls = array();
    
    /**
     * Uninstall SQL
     *
     * @var array
     */
    protected $_uninstall_sqls = array();    
    
    /**
     * Database adapter
     * 
     * @var KDatabaseAdapterAbstract
     */
    protected $_db;
    
    /**
     * An arary of migrations
     * 
     * @var array
     */
    protected $_versions;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->getService('koowa:loader')->loadIdentifier('com://admin/migrator.helper');
        
        $this->_component       = $config->component;        
        
        $this->_db      = $config->db;
        settype($this->_schemas, 'array');
        $config->mixer  = $this;                
        
        $this->mixin(new KMixinCommand($config));
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(           
           'db'                => $this->getService('koowa:database.adapter.mysqli'),
           'command_chain'     => $this->getService('koowa:command.chain'),
           'event_dispatcher'  => $this->getService('koowa:event.dispatcher'),
           'component'         => $this->getIdentifier()->package
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * Return the component name
     * 
     * @return string
     */
    public function getComponent()
    {
        return $this->_component;
    }
    
    /**
     * Set the current version
     * 
     * @param int $version
     * 
     * @return void
     */
    public function setCurrentVersion($version)
    {
        $version = min($this->getMaxVersion(), $version);
        $version = max($version, 0);
        $this->_getVersion()->version = $version;
        $this->_getVersion()->saveEntity();
    }
    
    /**
     * Return the current version
     * 
     * @return int
     */
    public function getCurrentVersion()
    {
        return (int)$this->_getVersion()->version;
    }
    
    /**
     * Return the max version
     * 
     * @return int
     */
    public function getVersions()
    {
        if ( !isset($this->_versions) )
        {       
            $versions = array();                        
            
            foreach($this->getMethods() as $method)
            {
                $matches = array();
            
                if ( preg_match('/(up)(\d+)/', $method, $matches) ) {
                    $versions[] = $matches[2];
                }
            }
            
            $path            = dirname($this->getIdentifier()->filepath);
            
            if ( file_exists($path) ) 
            {
                $files = new DirectoryIterator($path.'/migrations');
                foreach($files as $file) 
                {
                    $matches = array();                    
                    if ( preg_match('/(\d+)\.sql/', (string)$file, $matches)) {
                        $versions[] = $matches[1];
                    }
                    elseif ( preg_match('/(\d+)\.php/', (string)$file, $matches)) {
                        $versions[] = $matches[1];
                    }
                }
            }            
            $versions[]         = 0;
            sort($versions);
            $this->_versions    = array_unique($versions);         
            $this->_max_version = array_pop($versions);
        }
        
        return $this->_versions;   
    }
    
    /**
     * Return the max version
     * 
     * @return int
     */
    public function getMaxVersion()
    {
        $versions = $this->getVersions();
        return $this->_max_version;
    }
    
    /**
     * Migrate up
     * 
     * @param int version Up the version upgrate the data to 
     * 
     * @return void
     */
    public function up($steps = PHP_INT_MAX)
    {            
       $context = $this->getCommandContext();                     
       
       $versions = array();
       
       foreach($this->getVersions() as $version) 
       {           
           if ( $version > $this->getCurrentVersion() ) {
               if ( $step++ >= $steps) {
                   break;
               }               
               $versions[] = $version;
           }
       }

       $context->versions = $versions;
       
       $this->getCommandChain()->run('before.migration', $context);
       
       foreach($versions as $version)
       {
           $context->version = $version;           
           $this->getCommandChain()->run('before.version.up', $context);
           $this->_run('up', $version);
           $this->getCommandChain()->run('after.version.up', $context);
           $this->setCurrentVersion($version);
       }
       
       $this->getCommandChain()->run('after.migration', $context);
    }
    
    /**
     * Rollsback
     * 
     * void
     */
    public function rollback()
    {
        return $this->down();
    }
    
    /**
     * Migrate up
     *
     * @param int version Up the version upgrate the data to
     *
     * @return void
     */
    public function down($steps = 1)
    {        
       $context = $this->getCommandContext();                     
       
       $versions = array();
       $step     = 0;
       $v        = $this->getVersions();
       rsort($v);
       foreach($v as $version) 
       {        
           if ( $version <= $this->getCurrentVersion() ) 
           {
               $versions[] = $version;
               if ( $step++ >= $steps) {
                   break;
               }               
           }
       }
   
       $context->versions = $versions;
       
       $this->getCommandChain()->run('before.migration', $context);
       
       foreach($versions as $version)
       {
           $context->version = $version;           
           $this->getCommandChain()->run('before.version.down', $context);
           $this->_run('down', $version);
           $this->getCommandChain()->run('after.version.down', $context);
           $this->setCurrentVersion($version);
       }
       
       $this->getCommandChain()->run('after.migration', $context);                                
    }    
    
    /**
     * 
     * @param string $method
     * @param int $version
     */
    protected function _run($method, $version)
    {
        if ( $version > 0 )
        {
            $path = dirname($this->getIdentifier()->filepath).'/migrations';
            
            if ( method_exists($this, '_'.$method.$version) ) {
                $this->{'_'.$method.$version}();
            }
            
            if ( $method == 'up' ) {
                $path = $path.'/'.$version.'.sql';
            } else {
                $path = $path.'/'.$version.'.down.sql';
            }
            
            if ( file_exists($path) ) {
                dbexecfile($path);
            }            
        }
    }
    
    /**
     * Return the version
     * 
     * @return ComMigratorDomainEntityVersion
     */
    protected function _getVersion()
    {    
        if ( !isset($this->_version) )
        {
             $this->_version = $this->getService('repos://admin/migrator.version')
                        ->findOrAddNew(array('component'=>$this->_component));                        
        }
        return $this->_version;
    }
    
    /**
     * Add a query that's added to the schema.sql
     * 
     * @param string $query
     * 
     * @return void
     */
    public function addSchemaQuery($query)
    {
        $query = str_replace($this->_db->getTablePrefix(), '#__', $query);
        $this->_schema_sql[$query] = $query.';';  
        return $this;
    }
    
    /**
     * Add a query that's added to the uninstall.sql
     * 
     * @param string $query
     * 
     * @return void
     */
    public function addUninstallQuery($query)
    {
        $query = str_replace($this->_db->getTablePrefix(), '#__', $query);
        $this->_uninstall_sqls[$query] = $query.';';
        return $this;
    }

    /**
     * Creates the schema
     * 
     * @return void
     */
    public function createSchema()
    {
        $version              = $this->getCurrentVersion();
        foreach($this->_schemas as $schema)
        {
            $row = $this->_db->show('SHOW CREATE TABLE #__'.$schema,KDatabase::FETCH_ARRAY);
            $sql = $row['Create Table'];
            $this->addSchemaQuery($sql);
            $this->addUninstallQuery("DROP TABLE IF EXISTS `#__${schema}`");
        }
        $this->addSchemaQuery("UPDATE #__migrator_versions SET `version` = $version WHERE `component` = '{$this->_component}'");
        $this->addUninstallQuery("DELETE #__migrator_versions WHERE `component` = '{$this->_component}'");        
    }
    
    /**
     * Write the schema and uninstall queries
     * 
     * @return void
     */
    public function write()
    {                
        $path            = dirname($this->getIdentifier()->filepath);        
        file_put_contents($path.'/schema.sql',    implode("\n\n", $this->_schema_sql));
        file_put_contents($path.'/uninstall.sql', implode("\n\n", $this->_uninstall_sqls));
    }
}