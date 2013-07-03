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

jimport( 'joomla.installer.installer' );
jimport('joomla.installer.helper');
jimport('joomla.filesystem.file');

/**
 * Package Installer
 *
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Domain_Model
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBazaarDomainModelInstaller extends KObject 
{	
	/**
	 * File to install
	 * 
	 * @var file 
	 */
	protected $_file;
	
	/**
	 * The store
	 * 
	 * @var ComBazaarDomainModelStore
	 */
	protected $_store;
	
	/**
	 * Session Value
	 * 
	 * @var array 
	 */
	protected $_session;
	
	/**
	 * Intall Result
	 * 
	 * @var string
	 */
	protected $_message;
		
	/**
	 * Intall Result
	 * 
	 * @var string
	 */
	protected $_package;	
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_file 	= $config->file;		
		$this->_session = KConfig::unbox($config->session);
		$this->_store   = $config->store;
	}
		
	/**
	 * Tries to install the package from the bazar URL
	 *	 
	 * @return void
	 */
	public function install()
	{
	    $url     = $this->_store->getDownloadURL();		
		$url	.= '&file='.$this->_file;
		$config =& JFactory::getConfig();
		$header =  'Cookie: '.str_replace('"', '',KHelperArray::toString($this->_session,'=',' ',false,false));
		$target = $config->getValue('config.tmp_path').DS.basename($this->_file);
		$c  = curl_init();
		$options = array(
		  CURLOPT_URL => $url,
		  CURLOPT_HEADER => false,
		  CURLOPT_HTTPHEADER => array($header),
		  CURLOPT_RETURNTRANSFER => true
		
		);
		curl_setopt_array($c, $options);
		$output = curl_exec($c);		
        $response = curl_getinfo($c);
        
        curl_close($c);
        if ( $response['http_code'] == 200 && strlen($output) )
        {
            JFile::write($target, $output);        
        }  
        else 
        {
            $this->_message = 'Installation Failed';
            return false;
        }
		
		$this->_package = $package   = JInstallerHelper::unpack($target);
		
		$installer =& JInstaller::getInstance();
		
		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package			
			$result = false;
		} else {
			// Package installed sucessfully			
			$result = true;
		}

		// Cleanup the install files
		if (!is_file($package['packagefile'])) {
			$config =& JFactory::getConfig();
			$package['packagefile'] = $config->getValue('config.tmp_path').DS.$package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		
		if ( $result ) {
			$this->_message = $installer->message;
		}
		
		return $result;
	}
	
	/**
	 * Return the string
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		return $this->_message;
	}
	
	/**
	 * Return the string
	 * 
	 * @return string
	 */
	public function getPackage()
	{
		return $this->_package;
	}	
}