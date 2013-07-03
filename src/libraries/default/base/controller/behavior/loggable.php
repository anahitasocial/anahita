<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.error.log');

define('LOG_LEVEL_DEBUG' , 'DEBUG');
define('LOG_LEVEL_INFO' ,  'INFO');
define('LOG_LEVEL_WARN' ,  'WARN');
define('LOG_LEVEL_ERROR' , 'ERROR');

/**
 * Loggable Behavior 
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 * @uses JLog
 */
class LibBaseControllerBehaviorLoggable extends KControllerBehaviorAbstract
{
	/**
	 * Log instance
	 * 
	 * @var JLog
	 */
	protected $_log;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_log = $config->log;
	}
		
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$log = JLog::getInstance('system_log.php');
		
		$config->append(array(
			'log' => $log 
		));
	
		parent::_initialize($config);
	}
		
	/**
	 * Logs an entry 
	 * 
	 * @param array|string $entry
	 * @see JLog::addEntry options
	 * @return mixed
	 */	
	public function log($entry, $level = LOG_LEVEL_INFO)
	{
		if ( !is_array($entry) )
			$entry = array('comment'=>$entry, 'level'=>$level);

		$this->_log->addEntry($entry);
		return $this;
	}
}