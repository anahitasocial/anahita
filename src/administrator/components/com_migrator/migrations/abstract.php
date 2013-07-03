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
    const AUTO_DETECT_TABLES = -1; 
    
    /**
     * Array of table schmesa to dump into the install.sql
     * 
     * @var array
     */
    protected $_tables;
    
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
     * The output path to the schema file in
     * 
     * @var string
     */
    protected $_output_path;
    
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
        
        $this->_output_path   = $config->output_path;
                
        $this->_tables        = Kconfig::unbox($config->tables);
        
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
           'output_path'       => $path,
           'tables'            => self::AUTO_DETECT_TABLES,          
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
     * Set the component of the migration
     * 
     * @param string $component Component name
     * 
     * @return void
     */
    public function setComponent($component)
    {
        $this->_component = $component;
        return $this;
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
                if ( file_exists($path.'/migrations') )
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
            }                        
            sort($versions);
            $this->_versions    = array_unique($versions);         
            $this->_max_version = array_pop($versions);
        }
        
        return $this->_versions;   
    }
    
    /**
     * Retunr the number of versions behind
     * 
     * @return int
     */
    public function getVersionsBehind()
    {
        $current  = $this->getCurrentVersion();
        $versions = array_filter($this->getVersions(),
                function($version) use ($current) {
                    return $version > $current;
                }
        );
        return count($versions);        
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
     * Generates a migration. If no version is set then version is created
     * based on max version
     * 
     * @return void
     */
    public function generateMigration($version = null)
    {
        $path = dirname($this->getIdentifier()->filepath).'/migrations';
        $next = $this->getMaxVersion() + 1;
        $migration_file = "$path/$next.php";
        if ( !file_exists($migration_file) ) 
        {
            //create the directory
            if ( !file_exists(dirname($migration_file)) ) {
                mkdir(dirname($migration_file), 0755, true);
            }
            $class_name = 'Com'.ucfirst($this->getComponent()).'SchemaMigration'.$next;
            $component  = ucfirst($this->getComponent());
            $content    = <<<EOF
<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @package    Com_{$component}
 * @subpackage Schema_Migration
 */

/**
 * Schema Migration
 *
 * @package    Com_{$component}
 * @subpackage Schema_Migration
 */
class $class_name extends ComMigratorMigrationVersion
{
   /**
    * Called when migrating up
    */
    public function up()
    {
        //add your migration here
    }

   /**
    * Called when rolling back a migration
    */        
    public function down()
    {
        //add your migration here        
    }
}
EOF;
        file_put_contents($migration_file, $content);
        }
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
            
            if ( file_exists($path."/$version.php") ) 
            {
                $this->getService('koowa:loader')->loadFile($path."/$version.php");  
                $class = 'Com'.ucfirst($this->getIdentifier()->package).'SchemaMigration'.$version;
            } 
            else {
                $class = 'ComMigratorMigrationVersion';
            }
            
            $config = array(
              'db' => $this->_db,
              'version'            => $version,
              'service_container'  => $this->getService(),
              'service_identifier' => $this->getIdentifier(
                      'com://admin/'.$this->getIdentifier()->package.'.schema.migration.'.$version)
            );
            $migrator = new $class(new KConfig($config));
            $migrator->$method();                      
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
               //if table doesn't exists
                $this->_db->execute(<<<EOF
CREATE TABLE IF NOT EXISTS `#__migrator_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`)
)ENGINE=InnoDB;
EOF
                );
                $this->_version = $this->getService('repos://admin/migrator.version')
                    ->findOrAddNew(array('component'=>$this->_component));
        }
        return $this->_version;
    }

    /**
     * Return an array of table names
     * 
     * @return array
     */
    public function getTables()
    {
        if ( $this->_tables == self::AUTO_DETECT_TABLES )
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
            $schemas = $this->_tables;
            settype($schemas, 'array');
            $prefix  = $this->_db->getTablePrefix();
            $schemas = array_map(function($table) use($prefix) {
                return $prefix.$table;
            },$schemas);
        }
        return $schemas;
    }
    
    /**
     * Return the directory that we want the schema to be outputed to
     * 
     * @return string
     */
    public function getOutputPath()
    {
        return $this->_output_path;
    }
    
    /**
     * Set the output path
     * 
     * @param string $path output path
     * 
     * @return void
     */
    public function setOutputPath($path)
    {
        $this->_output_path = $path;
        return $this;
    }
    
    /**
     * Set the migration tables
     * 
     * @param array $tables
     * 
     * @return void
     */
    public function setTables($tables)
    {
        $this->_tables = $tables;
        return $this;
    }
    
    /**
     * Return the database adapter
     * 
     * @return KDatabaseAdapterAbstract
     */
    public function getDatabaseAdapter()
    {
         return $this->_db;       
    }
    
    /**
     * Set the database adapter
     * 
     * @param KDatabaseAdapterAbstract $adapter Database adapter
     * 
     * @return void
     */
    public function setDatabaseAdapter($adapter)
    {
         $this->_db = $adapter;       
    }
}