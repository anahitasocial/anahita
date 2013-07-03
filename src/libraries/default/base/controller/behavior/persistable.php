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

/**
 * Persistable Behavior
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorPersistable extends KControllerBehaviorAbstract
{
	/**
	 * Restores a state for an action
	 * 
	 * @param  string $action
	 * @return void
	 */
	public function restoreState($action)
	{
		//Built the session identifier based on the action
		$identifier  = $this->_mixer->getIdentifier().'.'.$action;
		$state       = KRequest::get('session.'.$identifier, 'raw', array());
	
		//Append the data to the request object
		$this->getState()->append($state);
	}
	
	/**
	 * Restores a state for an action
	 * 
	 * @param  string $action
	 * @return void
	 */
	public function persistState($action)
	{
		$state       = $this->getRequest();

		// Built the session identifier based on the action
		$identifier  = $this->_mixer->getIdentifier().'.'.$action;
		
		//Set the state in the session
		KRequest::set('session.'.$identifier, KConfig::unbox($state));
	}
	
	/**
	 * Load the model state from the request
	 *
	 * This functions merges the request information with any model state information
	 * that was saved in the session and returns the result.
	 *
	 * @param 	KCommandContext		The active command context
	 * @return 	void
	 */
	protected function _beforeControllerBrowse(KCommandContext $context)
	{
		$this->restoreState($context->action);
	}
	
	/**
	 * Saves the model state in the session
	 *
	 * @param 	KCommandContext		The active command context
	 * @return 	void
	 */
	protected function _afterControllerBrowse(KCommandContext $context)
	{
		$this->persistState($context->action);
	}
}