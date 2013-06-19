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
     * If set then it wil try to auto detect the schemas
     */
    const AUTO_DETECT_SCHEMA = -1; 
    
    /**
     * Array of table schmesa to dump into the install.sql
     * 
     * @var array
     */
    protected $_schemas;
    
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
     * That path to the schema file
     * 
     * @var string
     */
    protected $_schema_file;
    
    /**
     * That path to the uninstall file
     *
     * @var string
     */
    protected $_uninstall_file;    
    
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
        
        $config->mixer  = $this;                
        
        $this->_schema_file    = $config->schema_file;
        $this->_uninstall_file = $config->uninstall_file;
        $this->_schemas        = Kconfig::unbox($config->schemas);
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
        $path            = dirname($this->getIdentifier()->filepath);
        
        $config->append(array( 
           'schemas'           => self::AUTO_DETECT_SCHEMA,
           'schema_file'       => $path.'/schema.sql',
           'uninstall_file'    => $path.'/uninstall.sql',          
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
       $context    = $this->getCommandContext();
       $current    = $this->getCurrentVersion();
       $versions = array_filter($this->getVersions(), 
               function($version) use ($current) {
                   return $version > $current;
           }
       );
       
       sort($versions);
       $versions = array_slice($versions, 0, $steps);
       
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
     * Migrate up
     *
     * @param int version Up the version upgrate the data to
     *
     * @return void
     */
    public function down($steps = 1)
    {        
       $context    = $this->getCommandContext();
       $current    = $this->getCurrentVersion();
       $versions = array_filter($this->getVersions(), 
               function($version) use ($current) {
                   return $version <= $current;
           }
       );
       rsort($versions);
       $versions = array_slice($versions, 0, $steps);
       
       $context->versions = $versions;        
       $this->getCommandChain()->run('before.migration', $context);

       foreach($versions as $version) 
       {
            $values = $this->getVersions();
            $index  = array_search($version, $values);
            $next   = $index == 0 ? 0 : $values[$index-1];            
            $context->version = $next;
            $this->getCommandChain()->run('before.version.down', $context);    
            $this->_run('down', $version);
            $this->getCommandChain()->run('after.migration', $context);
            $this->setCurrentVersion($next);
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
        if ( $this->_schemas == self::AUTO_DETECT_SCHEMA ) 
        {
            $tables  = $this->_db->show('SHOW TABLES',KDatabase::FETCH_FIELD_LIST);
            $prefix  = $this->_db->getTablePrefix().$this->getIdentifier()->package.'_';
            $schemas = array();
            foreach($tables as $table) 
            {
                if ( strpos($table, $prefix) === 0 ) {
                    $schemas[] = $table;
                }
            }
        } else {
            $schemas = $this->_schemas;
            settype($schemas, 'array');
            $prefix  = $this->_db->getTablePrefix();            
            $schemas = array_map(function($table) use($prefix) {
                return $prefix.$table;
            },$schemas);
        }        
        $version = (int)$this->getMaxVersion();
        $version = (int)$this->getCurrentVersion();
        foreach($schemas as $schema)
        {
            $row = $this->_db->show('SHOW CREATE TABLE '.$schema,KDatabase::FETCH_ARRAY);
            $sql = $row['Create Table'];            
            $sql = preg_replace('/ AUTO_INCREMENT=\d+/','', $sql);
            $sql = preg_replace('/ TYPE=/','ENGINE=', $sql);
            $this->addSchemaQuery($sql);
            $this->addUninstallQuery("DROP TABLE IF EXISTS `${schema}`");
        }
        //if ( $version > 0 ) 
        {            
            $this->addSchemaQuery("UPDATE #__migrator_versions SET `version` = $version WHERE `component` = '{$this->_component}'");
        }        
        $this->addUninstallQuery("DELETE #__migrator_versions WHERE `component` = '{$this->_component}'");        
    }
    
    /**
     * Write the schema and uninstall queries
     * 
     * @return void
     */
    public function write()
    {
        if ( !empty($this->_schema_sql) && $this->_schema_file ) {
            file_put_contents($this->_schema_file,    implode("\n\n", $this->_schema_sql));
        }
        
        if ( !empty($this->_uninstall_sqls) && $this->_uninstall_file ) {
            file_put_contents($this->_uninstall_file, implode("\n\n", $this->_uninstall_sqls));
        }
    }
}