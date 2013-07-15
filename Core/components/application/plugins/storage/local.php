<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Storage
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Local storage plugin. 
 * 
 * @category   Anahita
 * @package    Plg_Storage
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgStorageLocal extends PlgStorageAbstract
{
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
        $base_uri  = JURI::base();
        
        if ( JFactory::getApplication()->getClientId() != 0 )
            $base_uri = preg_replace('/\w+\/$/', '', $base_uri);
        
        $config->append(array(
              'folder'     => 'assets',
              'base_uri'   => $base_uri,
               'root'      => JPATH_ROOT
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * {@inheritdoc}
     */
    protected function _read($path)
    {
        $path = $this->_realpath($path);
        return file_get_contents($path);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _write($path, $data, $public)
    {
        $path = $this->_realpath($path);
        
        $dir  = dirname($path);
        
        if ( !file_exists($dir) )
        {
            mkdir($dir, 0707, true);
        }
        
        return file_put_contents($path, (string) $data);
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _exists($path)
    {
        $path = $this->_realpath($path);
        return file_exists($path);
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _delete($path)
    {
        $path = $this->_realpath($path);
        jimport('joomla.filesystem.folder');
        if ( is_dir($path) )
        {
            JFolder::delete($path);
        }            
        elseif ( file_exists($path) ) 
        {
            @unlink($path);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _getUrl($path)
    {
        return $this->_params->base_uri.$path;
    }
    
    /**
     * Return the realpath path of a relative path 
     *
     * @param string $path The path to append
     * 
     * @return
     */
    protected function _realpath($relative)
    {
        return $this->_params->root.DS.str_replace('/', DS, $relative);
    }    
}