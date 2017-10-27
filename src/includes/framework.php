<?php

/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
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

KServiceIdentifier::setApplication('site', ANPATH_BASE);
KLoader::addAdapter(new AnLoaderAdapterComponent(array('basepath' => ANPATH_BASE)));
KServiceIdentifier::addLocator(KService::get('anahita:service.locator.component'));

KLoader::addAdapter(new KLoaderAdapterPlugin(array('basepath' => ANPATH_ROOT)));
KServiceIdentifier::addLocator(KService::get('koowa:service.locator.plugin'));

KLoader::addAdapter(new AnLoaderAdapterTemplate(array('basepath' => ANPATH_BASE)));
KServiceIdentifier::addLocator(KService::get('anahita:service.locator.template'));

KService::setAlias('anahita:domain.store.database', 'com:base.domain.store.database');
KService::setAlias('anahita:domain.space', 'com:base.domain.space');

//make sure for the autoloader to be reigstered after nooku
if (PHP_SAPI != 'cli') {
  $autoloader = require_once ANPATH_VENDOR.'/autoload.php';
  $autoloader->unregister();
  $autoloader->register();
}

KLoader::getInstance()->loadIdentifier('com://site/application.aliases');
KService::get('com:settings.setting');
