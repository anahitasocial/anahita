<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Installer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

/**
 * Core Installer Plugin 
 *
 * @category   Anahita
 * @package    Plg_Installer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgInstallerCore extends JPlugin
{
    /**
     * On setup install
     *
     * @param JInstaller 	  	$installer The Installer object
     *
     * @return void
     */
    public function onSetupInstall($installer, $type)
    {
        //register cli
        KServiceIdentifier::setApplication('cli', JPATH_ROOT.'/cli');
        require_once JPATH_ROOT.'/cli/components/com_migrator/helper.php';
        if ( $type == 'system' ) {
            $installer->setAdapter($type, new PlgInstallerSystem($installer));
        }
    }
    
    /**
     * Imports the migrator helper
     *
     * @return void
     */
    public function onBeforeUnInstallExtension($installer, $adapter, $identifier)
    {
        $this->_clearAPCCache();
        
         //removes the migration
        if ( $adapter instanceof JInstallerComponent )
        {
            $component = dbfetch('SELECT REPLACE(`option`,"com_","") FROM #__components WHERE id ='.$identifier, KDatabase::FETCH_FIELD);
            if ( $component )
            {
                $installer->set('extension_name', 'com_'.$component);
                KService::get('com://cli/migrator.store')->delete($component);
            }
        }
    }
    
    /**
     * After Installer Event.
     *
     * @param JInstaller 	  	$installer The Installer object
     * @param JInstallerAdapter  $adapter   The adapter that is installing the current extension. Can be Component,
     * Plugin, Language, Module
     * @param boolean		    $result	   The boolean flag of the instllation result
     *
     * @return void
     */
    public function onAfterInstallExtension($installer, $adapter, $result)
    {
        $this->_clearAPCCache();
        
        PlgInstallerHelper::onAfterInstallExtension($installer, $adapter, $result);
    }
    
    /**
     * Before Installer Event.
     *
     * @param JInstaller 	    $installer  The Installer object
     * @param JInstallerAdapter $adapter    The adapter that is installing the current extension. Can be Component,
     * Plugin, Language, Module
     * @param string		    $identifier The extension identifier.
     * @param int	            $client	   The client. Can be JSite or JAdmin
     * @param boolean           $result
     *
     * @return void
     */
    public function onAfterUnInstallExtension($installer, $adapter, $identifier, $client, $result)
    {
        $this->_clearAPCCache();
        
        PlgInstallerHelper::onAfterUnInstallExtension($installer, $adapter, $identifier, $client, $result);
    }    
    
    /**
     * If an overwrite it removes the existing destination 
     *
     * @return void
     */
    public function onBeforeInstallExtension($installer, $adapter, $identifier)
    {
        $this->_clearAPCCache();
        
        $document = @$installer->getManifest()->document;
        
        if ( !$document )
            return;
            
        $type = $document->attributes('type');
        
        if ( $installer->getOverwrite() && in_array($type,array('component','module','plugin')) )
        {
            if ( $type == 'component'  )
            {
                $name =& $document->getElementByPath('name');
                $name = 'com_'.strtolower(JFilterInput::clean($name->data(), 'cmd'));
                $files[] = JPath::clean(JPATH_ADMINISTRATOR.DS.'components'.DS.$name);
                $files[] = JPath::clean(JPATH_SITE.DS.'components'.DS.$name); 
            }
            elseif ( $type == 'module' )
            {
                if ($cname = $document->attributes('client')) 
                {
                    // Attempt to map the client to a base path
                    jimport('joomla.application.helper');
                    $client =& JApplicationHelper::getClientInfo($cname, true);
                } 
                
                if ( !empty($client) ) {
                    $basepath = $client->path;
                    $clientId = $client->id;                    
                } else {
                    $cname = 'site';
                    $basepath = JPATH_SITE;
                    $clientId = 0;                    
                }
                $element =& $document->getElementByPath('files');
                if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
                    $files = $element->children();
                    foreach ($files as $file) {
                        if ($file->attributes('module')) {
                            $mname = $file->attributes('module');
                            break;
                        }
                    }
                }  
                $files   = array();              
                $files[] = $basepath.DS.'modules'.DS.$mname;
            }
            elseif ( $type == 'plugin' )
            {
                $group = $document->attributes('group');
                
                if ( $group )
                {
                    $installer->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$group);
                    $installer->removeFiles($document->getElementByPath('images'), -1);
                    $installer->removeFiles($document->getElementByPath('files'), -1);
        
                    // Remove all media and languages as well
                    $installer->removeFiles($document->getElementByPath('media'));
                    $installer->removeFiles($document->getElementByPath('languages'), 1);                    
                }
                return;
            }

                
            foreach($files as $file)
            {
                if ( file_exists($file) )
                    JFolder::delete($file);
            }
                            
            $installer->removeFiles($document->getElementByPath('media'));
            $installer->removeFiles($document->getElementByPath('languages'));
            $installer->removeFiles($document->getElementByPath('administration/languages'), 1);            
        }
    }
    
    /**
     * Clears the Koowa APC cache.
     * 
     * @see clean_apc_with_prefix
     */
    protected function _clearAPCCache()
    {
        if ( function_exists('clean_apc_with_prefix') )
        {
            $prefix = md5(JFactory::getApplication()->getCfg('secret')).'-cache-koowa';
            clean_apc_with_prefix($prefix);
            clean_apc_with_prefix('cache_system');
            clean_apc_with_prefix('cache__system');
        }
    }
}