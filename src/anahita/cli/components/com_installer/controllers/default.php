<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita_Dev
 * @package    Com_Installer
 * @subpackage Controller
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
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInstallerControllerDefault extends LibBaseControllerResource
{
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
    }  
      
    /**
     * Installer
     *
     * @param KCommandContext $context Context parameter
     *
     * @return void
     */
    protected function _actionInstall(KCommandContext $context)
    {
        $data = $context->data;
        
        foreach($this->getAdapters($this->path) as $adapter) 
        {
            $adapter->install();
            print('Installted '.$adapter->getName())."\n";
        }
    }

    /**
     * Return the adapter
     * 
     * @param string $path Path to a folder 
     * 
     * @return arrays
     */
    public function getAdapters($path)
    {
        $path = realpath($path);
        jimport('joomla.filesystem.folder');
        if ( is_dir($path) ) {
             //find the xml files
             $manifests   = JFolder::files($path,'.xml');             
             foreach($manifests as $key => $manifest) {
                 $manifests[$key] = $path.'/'.$manifest;
             }
        } else
             $manifests[] = $path;
        $adapters = array();
        foreach($manifests as $manifest)
        {
            if ( !file_exists($manifest) ) {
                print('Manifest missing '.$manifest)."\n";
                continue;
            }
            $xml      = simplexml_load_file($manifest);
            $bundles  = array();
            if ( $bundles = $xml->xpath('bundles/bundle') ) 
            {
                $array = array();
                foreach($bundles as $key => $bundle) 
                {
                    $array = array_merge($array, $this->getAdapters($path.'/'.$bundle));
                }
                $bundles = $array;
            }
            $type = (string)$xml->attributes()->type;
            $identifier = clone $this->getIdentifier();
            $identifier->path = array('adapter');
            $identifier->name = $type;
            $adapters[] = $this->getService($identifier, array('path'=>$manifest));
            foreach($bundles as $bundle) {
                $adapters[] = $bundle;
            }
            
        }
        return $adapters;
       
    }
}