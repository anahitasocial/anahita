<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

define('ANAHITA', 1);

/**
 * Include Koowa
 */
require_once JPATH_LIBRARIES.'/koowa/koowa.php';

/**
 * Service Class
 * 
 * @category   Anahita
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class Anahita
{       
    /**
     * Path to Anahita libraries
     * 
     * @var string
     */
    protected $_path;
        
	/**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone() { }

	/**
     * Singleton instance
     *
     * @param  array  An optional array with configuration options.
     * @return Koowa
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }
    
    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     *
     * @param  array  An optional array with configuration options.
     */
    final private function __construct($config = array())
    {
        //store the path
        $this->_path = dirname(__FILE__);
                
        //instantiate koowa
        Koowa::getInstance(array(
            'cache_prefix'  => $config['cache_prefix'],
            'cache_enabled' => $config['cache_enabled']
        ));
        
        KLoader::addAdapter(new KLoaderAdapterModule(array('basepath' => JPATH_BASE)));
        KLoader::addAdapter(new KLoaderAdapterPlugin(array('basepath' => JPATH_ROOT)));

        KServiceIdentifier::addLocator(KService::get('koowa:service.locator.plugin'));
        
        KServiceIdentifier::setApplication('site' , JPATH_SITE);
        KServiceIdentifier::setApplication('admin', JPATH_ADMINISTRATOR);
        //register an empty path for the application
        //a workaround to remove the applicaiton path from an identifier
        KServiceIdentifier::setApplication('', '');
        
        require_once $this->_path.'/functions.php';
        require_once $this->_path.'/loader/adapter/anahita.php';
                
        //if caching is not enabled then reset the apc cache to
        //to prevent corrupt identifier        
        if ( !$config['cache_enabled'] ) {
              clean_apc_with_prefix($config['cache_prefix']);
        }   
   
        KLoader::addAdapter(new AnLoaderAdapterAnahita(array('basepath'=>$this->_path)));
        KLoader::addAdapter(new AnLoaderAdapterComponent(array('basepath'=>JPATH_BASE)));
        KLoader::addAdapter(new AnLoaderAdapterTemplate(array('basepath'=>JPATH_BASE)));
        KLoader::addAdapter(new AnLoaderAdapterDefault(array('basepath'=>JPATH_LIBRARIES.DS.'default')));
        
        KServiceIdentifier::addLocator(new AnServiceLocatorAnahita());
        KServiceIdentifier::addLocator( new AnServiceLocatorRepository() );
        KServiceIdentifier::addLocator( KService::get('anahita:service.locator.module') );
        KServiceIdentifier::addLocator( KService::get('anahita:service.locator.component') );
        KServiceIdentifier::addLocator( KService::get('anahita:service.locator.template') );
        
        AnServiceClass::getInstance();
        
        KService::get('koowa:loader')->loadClass('AnServiceClass');
                                
        //create a central event dispatcher           
        KService::set('anahita:event.dispatcher', KService::get('koowa:event.dispatcher'));
        
        //create an event command with central event dispatcher
        KService::set('anahita:command.event', 
                    KService::get('koowa:command.event', array('event_dispatcher'=>KService::get('anahita:event.dispatcher'))));
        

        //setup aliases
        KService::setAlias('koowa:database.adapter.mysqli', 'com://admin/default.database.adapter.mysqli');
        KService::setAlias('anahita:domain.store.database', 'com:base.domain.store.database');        
        KService::setAlias('anahita:domain.space',          'com:base.domain.space');
    }
    
    /**
     * Get the version of the Anahita library
     * 
     * @return string
     */
    public function getVersion()
    {
   	    $manifest = self::getManifest();
   	    
   	    if ( $manifest )
   	        return $manifest->getElementByPath('version')->data();
    }
    
    /**
     * Return whether the release is birth or not
     * 
     * @return boolean
     */
    public function isBirth()
    {
        $manifest = self::getManifest();
        
        if ( $manifest ) {
            return trim(strtolower($manifest->getElementByPath('release')->data())) == 'birth';
        }   
        
        return false;     
    }
    
    /**
     * Get path to Anahita libraries
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Returns the Anahita Manifest
     *
     * @return SimpleXML
     */
    public static function getManifest()
    {
        static $_manifest;
    
        if ( !$_manifest )
        {
            $_manifest = new JSimpleXML();
            $_manifest->loadFile(JPATH_ROOT.DS.'manifest.xml');
            $_manifest = $_manifest->document;
        }
    
        return $_manifest;
    }    
}