<?php
/**
 * @version ? 1.5.0 April 26, 2011
 * @author ? ?RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license ? http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');

define('ROKUPDATER_DLMETHOD_FOPEN', 0);
define('ROKUPDATER_DLMETHOD_CURL', 1);
define('ROKUPDATER_VERSION', 1.0);
define('ROKUPDATER_EXTRACTOR_16', 0);
define('ROKUPDATER_EXTRACTOR_15', 1);
define('ROKUPDATER_EXTRACTOR_PEAR', 2);

require_once(dirname(__FILE__).'/libraries/installer/RokStarInstaller.php');

class RokUpdater
{

    const ROKUPDATER_DLMETHOD_FOPEN = 0;
    const ROKUPDATER_DLMETHOD_CURL = 1;
    const ROKUPDATER_VERSION = 1.0;
    const ROKUPDATER_EXTRACTOR_16 = 0;
    const ROKUPDATER_EXTRACTOR_15 = 1;
    const ROKUPDATER_EXTRACTOR_PEAR = 2;

    var $current_version;
    var $tmp_path;
    var $details_url;
    var $details_path;
    var $details_cache_time;
    var $download_method;
    var $params;
    var $extractor;
    var $versions_path;
    var $versions_name;
    var $slug;


    function __construct()
    {
        //turn OFF php errors, we'll handle them prettily 
        //error_reporting(0);

    }

    function init($details_url, $slug, $params, $download_method = ROKUPDATER_DLMETHOD_FOPEN, $extractor_method = ROKUPDATER_EXTRACTOR_16)
    {

        $config =& JFactory::getConfig();
        $this->tmp_path = $config->getValue('config.tmp_path');

        $this->details_url = $details_url;
        $this->slug = $slug;
        $this->download_method = $download_method;
        $this->extractor = $extractor_method;
        $this->params = $params;
        $this->details_cache_time = 1200;
        $this->details_path = $this->tmp_path . DS . basename($details_url);
        $this->versions_path = JPATH_ROOT . DS . 'media' . DS . 'rokupdater' . DS . $slug . '-version.json';

    }

    function updateAvailable()
    {

       
        $params =& $this->params;
        RokUpdater::updateVersion($params->get('update_name'), $params->get('update_slug'), $params->get('current_version'), $this->details_url);
     
        // read version file
        $version = json_decode(JFile::read($this->versions_path));
        $this->current_version = $version->current_version;

        $alldetails = $this->_decodeJSONData($this->details_path);

        foreach ($alldetails as $key => $details) {
            if ($key == $version->slug) {
                $details->current_version = $this->current_version;

                foreach ($details->versions as $newversion) {
                    if(version_compare($this->current_version,$newversion->minimum_version,'>=') &&
                       version_compare($newversion->version, $this->current_version,'>')) {
                       $details->updates = true;
                       $details->version = $newversion->version;
                    } else {
                        $details->updates = false;
                    }
                    return $details;
                }
            }
        }
        return false;
    }

    function _decodeJSONData($path)
    {
        // determine if file should be refreshed
        $filemtime = @filemtime($path);

        // if file doesn't exist or expiration passed
        if (!$filemtime or (time() - $filemtime >= $this->details_cache_time)) {

            $result = RokUpdater::downloadFile($this->details_url, $this->details_path);
            if (is_object($result)) {
                HTML_RokError::showError('Download Failed: ' . $result->message . '(' . $result->number . ')');
                return false;
            }
        }

        $details = json_decode(JFile::read($this->details_path));

        return $details;

    }

    function updateVersion($name, $slug, $version, $details_url)
    {

        $version_data = new stdClass();
        $version_data->name = $name;
        $version_data->slug = $slug;
        $version_data->current_version = $version;
        $version_data->details_url = $details_url;
        $json = json_encode($version_data);

        JFile::write($this->versions_path, $json);

    }

    function installUpdate()
    {

        if (!file_exists($this->versions_path)) {
            $params =& $this->params;
            RokUpdater::updateVersion($params->get('update_name'), $params->get('update_slug'), $params->get('current_version'), $this->details_url);
        }
        // read version file

        jimport('joomla/filesystem.file');
        $version = json_decode(JFile::read($this->versions_path));
        $this->current_version = $version->current_version;

        $alldetails = $this->_decodeJSONData($this->details_path);

        foreach ($alldetails as $key => $details) {
            if ($key == $version->slug) {

                foreach ($details->versions as $newversion) {
                    if(version_compare($this->current_version,$newversion->minimum_version,'>=')) {
                       $details->download_url = $newversion->download_url;
                       $details->version = $newversion->version;
                       $details->md5 = $newversion->md5;
                    }
                }

                $file_path = $this->tmp_path;
                $result = RokUpdater::downloadFile($details->download_url, $file_path);
                $final_path = $file_path.DS.$result;

                if (is_object($result)) {
                    HTML_RokError::showError('Download Failed: ' . $result->message . '(' . $result->number . ')</p>');
                    return false;
                }

                if (md5_file($final_path) != $details->md5) {
                    HTML_RokError::showError('MD5 Mismatch: The MD5 hash for the downloaded file does not match the anticipated value.</p>');
                    return false;
                }


                $extractor = $this->extractor;
                $install_path = $this->tmp_path;

                // Temporary folder to extract the archive into
                $tmpdir = uniqid('install_');

                $install_path = $install_path . DS . $tmpdir;

                switch ($extractor)
                {
                    case ROKUPDATER_EXTRACTOR_16:
                        RokUpdater::import('joomla.filesystem.archive');
                        if (!JArchive::extract($final_path, $install_path)) {
                            HTML_RokError::showError('Failed to extract archive!');
                            return false;
                        }
                        break;

                    case ROKUPDATER_EXTRACTOR_15:
                        jimport('joomla.filesystem.archive');
                        if (!JArchive::extract($final_path, $install_path)) {
                            HTML_RokError::showError('Failed to extract archive!');
                            return false;
                        }
                        break;

                    case ROKUPDATER_EXTRACTOR_PEAR:
                        jimport('pear.archive_tar.Archive_Tar');
                        $extractor = new Archive_Tar($install_path);
                        if (!$extractor->extract(JPATH_SITE)) {
                            HTML_RokError::showError('Failed to extract archive!');
                            return false;
                        }
                        break;
                }


                jimport('joomla.installer.installer');
                jimport('joomla.installer.helper');
                jimport('joomla.filesystem.folder');
                jimport('joomla.filesystem.file');

                // Get an installer instance
                $installer =& RokStarInstaller::getInstance();

                // Run the installer and catch any output
                ob_start();
                $ret = $installer->install($install_path);
                $output = ob_get_clean();

                $errors = JError::getErrors();
                
                JFolder::delete($install_path);
                JFile::delete($final_path);


                RokUpdater::updateVersion($details->name, $version->slug, $details->version, $this->details_url);
                return $ret;
            }

        }


    }

    function downloadFile($url, $target)
    {
        RokUpdater::import('pasamio.downloader.downloader');
        $downloader =& Downloader::getInstance();
        //$error_object = new stdClass();


        $adapter = null;
        switch ($this->download_method)
        {
            case ROKUPDATER_DLMETHOD_FOPEN:
            default:
                $adapter = $downloader->getAdapter('fopen');
                break;
            case ROKUPDATER_DLMETHOD_CURL:
                $adapter = $downloader->getAdapter('curl');
                break;
        }

        return $adapter->downloadFile($url, $target, $this->params);
    }

    function generateUAString($mask = true)
    {
        $version = new JVersion();
        $lang =& JFactory::getLanguage();
        $parts = Array();
        if ($mask) {
            $parts[] = 'Mozilla/5.0';
        } else {
            $parts[] = 'Joomla!';
        }
        $parts[] = '(Joomla; PHP; ' . PHP_OS . '; ' . $lang->getTag() . '; rv:1.9.1)';
        $parts[] = 'Joomla/' . $version->getShortVersion();
        $parts[] = 'RokUpdater/' . ROKUPDATER_VERSION;
        return implode(' ', $parts);
    }

    function import($path)
    {
        // attempt to load the path locally but...
        // unfortunately 1.5 doesn't check the file exists before including it so we mask it
        $res = JLoader::import($path, dirname(__FILE__) . DS . 'libraries');
        if (!$res) {
            // fall back when it doesn't work
            return jimport($path);
        }
        return $res;
    }

}

class HTML_RokError
{
    function showError($message)
    {
        echo '<p class="mc-error">' . $message . '</p>';
    }
}


