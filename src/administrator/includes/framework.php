<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */
// no direct access
defined( 'JPATH_BASE' ) or die( 'Restricted access' );

defined( '_JEXEC' ) or define('_JEXEC', 1);

define( 'DS', DIRECTORY_SEPARATOR );

define( 'JPATH_ROOT',           dirname(JPATH_BASE));

define( 'JPATH_SITE',           JPATH_ROOT );
define( 'JPATH_CONFIGURATION',  JPATH_ROOT );
define( 'JPATH_ADMINISTRATOR',  JPATH_ROOT.DS.'administrator' );
define( 'JPATH_XMLRPC',         JPATH_ROOT.DS.'xmlrpc' );
define( 'JPATH_LIBRARIES',      JPATH_ROOT.DS.'libraries' );
define( 'JPATH_PLUGINS',        JPATH_ROOT.DS.'plugins'   );
define( 'JPATH_INSTALLATION',   JPATH_ROOT.DS.'installation' );
define( 'JPATH_THEMES',         JPATH_BASE.DS.'templates' );
define( 'JPATH_CACHE',          JPATH_BASE.DS.'cache' );

/*
 * Installation check, and check on removal of the install directory.
 */
if (!file_exists( JPATH_CONFIGURATION.'/configuration.php' ) || (filesize( JPATH_CONFIGURATION.'/configuration.php' ) < 10) ) {
    echo 'No configuration file found. Exciting...';
    exit();
}

// Joomla : setup
require_once(JPATH_LIBRARIES . '/joomla/import.php');

jimport( 'joomla.application.application' );
jimport( 'joomla.application.menu' );
jimport( 'joomla.user.user');
jimport( 'joomla.environment.uri' );
jimport( 'joomla.html.html' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.event.event');
jimport( 'joomla.event.dispatcher');
jimport( 'joomla.language.language');
jimport( 'joomla.utilities.string' );

require_once JPATH_CONFIGURATION . '/configuration.php';

require_once( JPATH_LIBRARIES.'/anahita/anahita.php');

$config = new JConfig();

//instantiate anahita and nooku
Anahita::getInstance(array(           
    'cache_prefix'  => md5($config->secret).'-cache-koowa',
    'cache_enabled' => $config->caching
));

KServiceIdentifier::setApplication('site' , JPATH_SITE);
KServiceIdentifier::setApplication('admin', JPATH_ADMINISTRATOR);
        
KLoader::addAdapter(new AnLoaderAdapterComponent(array('basepath'=>JPATH_BASE)));
KServiceIdentifier::addLocator( KService::get('anahita:service.locator.component') );

KLoader::addAdapter(new KLoaderAdapterModule(array('basepath' => JPATH_BASE)));
KServiceIdentifier::addLocator( KService::get('anahita:service.locator.module') );

KLoader::addAdapter(new KLoaderAdapterPlugin(array('basepath' => JPATH_ROOT)));
KServiceIdentifier::addLocator(KService::get('koowa:service.locator.plugin'));

KService::setAlias('koowa:database.adapter.mysqli', 'com://admin/default.database.adapter.mysqli');
KService::setAlias('anahita:domain.store.database', 'com:base.domain.store.database');
KService::setAlias('anahita:domain.space',          'com:base.domain.space');

KLoader::getInstance()->loadIdentifier('com://admin/application.aliases');
?>
