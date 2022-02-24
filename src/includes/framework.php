<?php

/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */

// no direct access
defined('ANPATH_BASE') or die('Restricted access');
defined('_ANEXEC') or define('_ANEXEC', 1);

define('DS',                    DIRECTORY_SEPARATOR);
define('ANPATH_ROOT',           ANPATH_BASE);
define('ANPATH_SITE',           ANPATH_ROOT);
define('ANPATH_CONFIGURATION',  ANPATH_ROOT);
define('ANPATH_LANGUAGE',       ANPATH_ROOT.DS.'language');
define('ANPATH_LIBRARIES',      ANPATH_ROOT.DS.'libraries');
define('ANPATH_PLUGINS',        ANPATH_ROOT.DS.'plugins');
define('ANPATH_COMPONENTS',     ANPATH_ROOT.DS.'components');
define('ANPATH_INSTALLATION',   ANPATH_ROOT.DS.'installation');
define('ANPATH_THEMES',         ANPATH_BASE.DS.'templates');
define('ANPATH_CACHE',          ANPATH_BASE.DS.'cache');
define('ANPATH_VENDOR',         ANPATH_BASE.DS.'vendor');

/*
 * Installation check, and check on removal of the install directory.
 */
if (! file_exists(ANPATH_CONFIGURATION.'/configuration.php') || (filesize(ANPATH_CONFIGURATION.'/configuration.php') < 10)) {
    echo 'No configuration file found. Exiting...';
    exit();
}

// Platform : setup
require_once ANPATH_LIBRARIES.'/anahita/anahita.php';

//instantiate anahita
Anahita::getInstance();

AnServiceIdentifier::setApplication('site', ANPATH_BASE);
AnLoader::addAdapter(new AnLoaderAdapterComponent(array('basepath' => ANPATH_BASE)));
AnServiceIdentifier::addLocator(AnService::get('anahita:service.locator.component'));

AnLoader::addAdapter(new AnLoaderAdapterPlugin(array('basepath' => ANPATH_ROOT)));
AnServiceIdentifier::addLocator(AnService::get('anahita:service.locator.plugin'));

AnLoader::addAdapter(new AnLoaderAdapterTemplate(array('basepath' => ANPATH_BASE)));
AnServiceIdentifier::addLocator(AnService::get('anahita:service.locator.template'));

AnService::setAlias('anahita:domain.store.database', 'com:base.domain.store.database');
AnService::setAlias('anahita:domain.space', 'com:base.domain.space');

$autoloader = require_once ANPATH_VENDOR.'/autoload.php';
$autoloader->unregister();
$autoloader->register();

AnLoader::getInstance()->loadIdentifier('com://site/application.aliases');
AnService::get('com:settings.config');
