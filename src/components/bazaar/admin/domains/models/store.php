<?php

/** 
 * LICENSE: 
 * 
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Domain_Model
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

define('BAZAAR_DEFAULT_HOST', 'http://www.anahitapolis.com');

/**
 * Represents a bazaar
 *
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Domain_Model
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBazaarDomainModelStore extends KObject 
{
    /**
     * The store name
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * Host
     *
     * @var string
     */
    protected $_host;    
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config = null)
    {
        if ( !$config )
            $config = new KConfig();
        
        parent::__construct($config);
        
        $this->_name = $config->name;
        $this->_host = $config->host;
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
            'name' => get_config_value('bazaar.store', Anahita::isBirth() ? '' : 'embryo-releases'), //default store
            'host' => get_config_value('bazaar.host', BAZAAR_DEFAULT_HOST)
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Returns the full URL
     *
     * @return void
     */
    public function getListURL()
    {
        $url = $this->_host.'/index.php?option=com_bzserver&view=packages&tmpl=component';
        
        if ( $this->_name )
            $url .= '&store='.$this->_name;
       
        return $url;
    }
    
    /**
     * Returns the download URL for a file
     * 
     * @return string
     */
    public function getDownloadURL()
    {
        $url = $this->_host.'/index.php?option=com_bzserver&view=package&action=download';
        
        if ( $this->_name )
            $url .= '&store='.$this->_name;
        
        return $url;        
    }
    
    /**
     * Return the store name
     * 
     * @return string
     */
    public function getName()
    {
         return $this->_name;   
    }
    
    /**
     * Sets the name
     * 
     * @param string $name Set the name of the store
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Return the store url
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }
    
    /**
     * Sets the name
     *
     * @param string $host The host of the bazaar
     *
     * @return void
     */
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }    
}