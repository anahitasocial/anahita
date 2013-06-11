<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita_Dev
 * @package    Com_Installer
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Installer Controller
 *
 * @category   Anahita_Dev
 * @package    Com_Installer
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComInstallerAdapterAbstract extends KObject
{
    /**
     * XML Element
     * 
     * @var SimpleXMLElement
     */
    protected $_xml;
    
    /**
     * XML Path
     *
     * @var string
     */
    protected $_xml_file;    
    
    /**
     * Path
     *
     * @var string
     */
    protected $_path;

    /**
     * Table Name
     *
     * @var string
     */
    protected $_table_name;    
    
    /**
     * Database
     * 
     * @var KDatabaseAbstract
     */
    protected $_db;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        $this->_xml_file = $config->path;        
        $this->_path     = dirname($config->path);
        $this->_xml      = simplexml_load_file($config->path);
                
        parent::__construct($config);
        
        $this->_table_name = $config->table_name;
        $this->_db         = KService::get('koowa:database.adapter.mysqli');
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
           
        ));
    
        parent::_initialize($config);
    }

    /**
     * Return the name of the adapter
     *
     * @return string
     */
    public function getName()
    {
        $name = (string)$this->_xml->name;
        return $name;
    }
    
    /**
     * Installs the extension
     *
     * @return void
     */
    public function install()
    {
        if ( isset($this->_xml->media) )
            $this->_copyMedia($this->_xml->media, JPATH_SITE.'/media/');
    }
    
    /**
     * Copy the media
     * 
     * @param  SimpleXMLElement $media       The media element
     * @param  string           $destination The destination to copy the file
     * 
     * @return void
     */
    protected function _copyMedia($media, $destination)
    {
        if ( isset($media) && @count($media->attributes()) )
        {
            $source = $this->_path.'/'.$media->attributes()->folder;
            $dest   = $destination.'/'.$media->attributes()->destination;            
            $this->_copy($source, $dest);
        }
    }
    
    /**
     * Copy the libraries
     *
     * @param  SimpleXMLElement $libs        The libs element
     * @param  string           $destination The destination to copy the file
     *
     * @return void
     */
    protected function _copyLibs($libs, $destination)
    {       
        if ( isset($libs) && @count($libs->attributes()) )
        {
            $source = $this->_path.'/'.$libs->attributes()->folder;
            foreach($libs->children() as $lib)
            {
                $source = $source.'/'.$lib;
                $dest   = $destination.'/'.$lib;
                $this->_copy($source, $dest);
            }
        }
    }    
    
    /**
     * Copy the lang
     * 
     * @param  SimpleXMLElement $langs The langs
     * @param  string           $destination The destination to copy the file
     * 
     * @return void
     */
    protected function _copyLangs($langs, $destination)
    {
        if ( isset($langs) && @count($langs->attributes()))
        {
            $source = $this->_path.'/'.$langs->attributes()->folder;
            foreach($langs->children() as $lang)
            {
                $src = $source.'/'.$lang;
                $dst = $destination.'/'.$lang->attributes()->tag.'/'.$lang;
                $this->_copy($src, $dst);
            }
        }
    }    
    
    /**
     * Symlinks source to destination
     *
     * @param string $source      The source path 
     * @param string $destination The destination folder
     * 
     * @return void
     */
    protected function _copy($source, $destination)
    {
        print "symlinking \n$destination to \n$source\n";
        @unlink($destination);
        if ( !is_dir($destination) && !file_exists(dirname($destination)) ) {
            mkdir(dirname($destination), 0707, true);
        }
        if ( !file_exists($destination) )
            @symlink($source, $destination);
    }
    
    /**
     * Symlinks source to destination
     *
     * @param string $source      The source path
     * @param string $destination The destination folder
     *
     * @return void
     */
    protected function _uncopy($source, $destination)
    {
        
    }  

    /**
     * Check if an extension exists 
     *
     * @param array $condition The condition to check
     * 
     * @return boolean
     */
    protected function _extExists($condition)
    {
        $query  = $this->_db->getQuery();
        $query->select('id');
        foreach($condition as $key => $value)
        {
            $query->where($key,'LIKE',(string)$value);
        }
        $query->from($this->_table_name);
        return $this->_db->select($query, KDatabase::FETCH_FIELD);        
    }
    
    /**
     * Insert an extension
     *
     * @param array $values The values 
     *
     * @return int Return the insertid
     */    
    protected function _extInsert($values)
    {
        return $this->_db->insert($this->_table_name, $values);        
    }
}