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
require_once 's3lib.php';

/**
 * Amazon S3 storage plugin. 
 * 
 * @category   Anahita
 * @package    Plg_Storage
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgStorageS3 extends PlgStorageAbstract
{   
    /**
     * S3 storage
     * 
     * @var S3
     */ 
    protected $_s3;
    
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
             'bucket'     => '',
             'access_key' => '',
             'secret_key' => '',
             'folder'     => 'assets',
             'use_ssl'    => false
        ));
        
        parent::_initialize($config);
        
        $this->_s3 = new S3($config->access_key, $config->secret_key, $config->use_ssl);
    }
        
    /**
     * {@inheritdoc}
     */
    protected function _read($path)
    {
        return $this->_s3->getObject($this->_params->bucket, $path)->body;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _write($path, $data, $public)
    {
        $acl	 = $public ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE;
        $options = array('Content-Type'=>$this->_s3->__getMimeType($path));
        return $this->_s3->putObject( $data, $this->_params->bucket, $path, $acl, array(), $options);
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _exists($path)
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _delete($path)
    {
        $paths = $this->_s3->getBucket($this->_params->bucket, $path);
    
        foreach($paths as $path) {
            $this->_s3->deleteObject($this->_params->bucket, $path['name']);
        }
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function _getUrl($path)
    {
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
        {
            $url = 'https://s3.amazonaws.com/'.$this->_params->bucket.'/'.$path;
        } else
        {
            $url = 'http://'.$this->_params->bucket.'.s3.amazonaws.com/'.$path;
        }
        return $url;
    }    
}