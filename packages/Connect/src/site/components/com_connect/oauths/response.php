<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

require_once('core.php');

/**
 * Service Response
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectOauthResponse extends KConfig
{
	/**
	 * Response Text
	 * 
	 * @var text
	 */
	protected $_text;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct($text, $config)
	{
		$this->_text = pick($text, '');
		
		parent::__construct($config);
	}
	
	/**
	 * Return the response code
	 * 
	 * @return int
	 */
	public function getCode()
	{
		return $this->http_code;
	}
	
	/**
	 * Return whether the response is succesful
	 * 
	 * @return boolean
	 */
	public function successful()
	{
		return in_range($this->getCode(), 200, 299);
	}
	
	/**
	 * Parse the resposne as query and return KConfig
	 * 
	 * @return KConfig
	 */
	public function parseQuery()
	{
		$array	  = array();						
		parse_str($this, $array);
		return new KConfig($array);		
	}
	
	/**
	 * Parse the resposne as json and return KConfig
	 * 
	 * @return KConfig
	 */
	public function parseJSON()
	{
		return new KConfig(json_decode((string)$this, true));
	}

	/**
	 * Parse the resposne as xml and return KConfig
	 * 
	 * @return SimpleXMLElement
	 */
	public function parseXML()
	{
		return new SimpleXMLElement((string)$this);
	}	
	
	/**
	 * Return the response text
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return pick($this->_text, ' ');
	}
}