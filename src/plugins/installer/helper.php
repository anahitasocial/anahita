<?php 

/**
 * LICENSE:
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

/**
 * Core Installer Helper 
 *
 * @category   Anahita
 * @package    Plg_Installer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgInstallerHelper
{    
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
    static public function onAfterInstallExtension($installer, $adapter, $result)
    {
        $path = $installer->getPath('source').'/delete.txt';
        
        if ( $result && file_exists($path) )
        {
            $files = explode("\n", file_get_contents($path));
            foreach($files as $key => $file) {
                $files[$key] = JPATH_ROOT.$file;
            }
            foreach($files as $file)
            {
                if ( file_exists($file) )
                {
                    if ( is_dir($file) )
                        JFolder::delete($file);
                    else
                        JFile::delete($file);
                }
            }
        }
            
        self::_legacy16to2($installer, $adapter);
        
        if ( $result && $adapter instanceof JInstallerComponent ) {
            //check if there's a delegate file and then install it as an app
            KService::get('koowa:loader')->loadIdentifier('com://admin/apps.domain.model.app');
            ComAppsDomainModelApp::syncApps();
        }
    
        if ( $result && $installer->getManifest()->document )
        {
            $bundles = $installer->getManifest()->document->getElementByPath('bundles');
            if ( $bundles )
                foreach($bundles->children() as $bundle)
                {
                    $path = $installer->getPath('source').'/'.$bundle->data();
                    $bundle = new JInstaller();
                    $bundle->install($path);
                }
        }
    
        if  ( $result && $installer->getManifest() )
        {
            $libraries = $installer->getManifest()->document->getElementByPath('libraries');
            if ( $libraries )
            {
                foreach($libraries->children() as $library)
                {
                    $src  = $installer->getPath('source').'/'.$libraries->attributes('folder').'/'.$library->data();
                    $dest = JPATH_LIBRARIES.'/'.$library->data();
                    JFolder::copy($src, $dest,'',true);
                }
            }
    
            $migration  = $installer->getManifest()->document->getElementByPath('migration');
    
            if ( $migration )
                $folder = $migration->attributes('folder');
            else
                $folder = 'migration';
    
            $base   =  $installer->getPath('source').'/'.$folder;
            if ( file_exists($base) )
            {
                if ( !KServiceIdentifier::getApplication('cli') ) {
                    KServiceIdentifier::setApplication('cli', JPATH_ROOT.'/cli');
                }                
                $files = JFolder::files($base,'\.php$');
                foreach($files as $file)
                {
                    $path = $base.'/'.$file;
                    $migrator = KService::get('com://cli/migrator.controller', array('path'=>$path));
                    if ( !JDEBUG )
                        capture();
                    $migrator->up();
                    if ( !JDEBUG )
                        end_capture();
                }
            }
        }
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
    static public function onAfterUnInstallExtension($installer, $adapter, $identifier, $client, $result)
    {
        $name = $installer->get('extension_name');
        
        if ( $name && $adapter instanceof JInstallerComponent ) {
            //check if there's a delegate file and then install it as an app
            KService::get('koowa:loader')->loadIdentifier('com://admin/apps.domain.model.app');                        
            ComAppsDomainModelApp::syncApps();
            KService::get('anahita:domain.space')
                ->removeNodesWithComponent($name);
        }
    } 
    
    
    /**
     * Handle a legecy update from 16 to 2
     * 
     * @param JInstaller        $installer The Installer object
     * @param JInstallerAdapter  $adapter   The adapter that is installing the current extension. Can be Component,
     * Plugin, Language, Module
     * 
     * @return void
     */
    static protected function _legacy16to2($installer, $adapter)
    {
        if ( is_callable("Anahita::initialize") && $adapter instanceof PlgInstallerSystem && version_compare(@$installer->getManifest()->document->version[0]->data(),'2','>='))
        {
            global $kfactory_legacy;
            $kfactory_legacy = true;
            KFactory::get('lib.koowa.database')->getCommandChain()->disable();
            if ( !function_exists('dboutput') )
                require_once JPATH_ROOT.'/cli/components/com_migrator/helper.php';
            require_once $installer->getPath('source').DS.'migration'.DS.'anahita.php';
            //check if the migration table exist or not
            if ( !dbexists('SHOW TABLES LIKE "jos_migrator_migraitons"') )
            {
               $sql = <<<EOF
                CREATE TABLE IF NOT EXISTS `#__migrator_migraitons` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `migrations` TEXT NULL DEFAULT '',
                      PRIMARY KEY  (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;
EOF;
                    dbexec($sql);
                }
                //insert the first record            
            if ( !dbfetch("SELECT * FROM #__migrator_migraitons", KDatabase::FETCH_ARRAY) ) {
                dbexec("INSERT INTO #__migrator_migraitons VALUES(NULL,'')");
            }
            $row     = dbfetch("SELECT * FROM #__migrator_migraitons", KDatabase::FETCH_ARRAY);
            $params  = new JParameter('',$row['migrations']);
            $i       = $params->get('anahita',0);
            $functions = array();
            while(true)
            {
                $i++;
                $func_name = 'anahita_'.$i;
                if ( !function_exists($func_name) )
                    break;
                $functions[] = $func_name;
            }
            foreach($functions as $func)
            {
                call_user_func($func);
            }
            $params->set('anahita', count($functions));
            dbexec("UPDATE #__migrator_migraitons SET migrations = '".$params->toString()."' WHERE id = ".$row['id']);
            JFactory::getApplication()->redirect('index.php?view=apps&option=com_bazaar','Update Complete','success');
            //sync the apps
            ComAppsDomainModelApp::syncApps();
        }
    }
}