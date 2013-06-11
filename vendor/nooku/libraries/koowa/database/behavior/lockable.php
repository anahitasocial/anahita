<?php
/**
 * @version 	$Id: lockable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Lockable Behavior
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
class KDatabaseBehaviorLockable extends KDatabaseBehaviorAbstract
{
	/**
	 * The lock lifetime
	 *
	 * @var integer
	 */
	protected $_lifetime;

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommand::PRIORITY_HIGH,
    	    'lifetime'	 => '900' //in seconds
	  	));

	  	$this->_lifetime = $config->lifetime;

    	parent::_initialize($config);
   	}

	/**
	 * Get the methods that are available for mixin based
	 *
	 * This function conditionaly mixies the behavior. Only if the mixer
	 * has a 'locked_by' property the behavior will be mixed in.
	 *
	 * @param object The mixer requesting the mixable methods.
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();

		if(isset($mixer->locked_by) && isset($mixer->locked_on)) {
			$methods = parent::getMixableMethods($mixer);
		}

		return $methods;
	}

	/**
	 * Lock a row
	 *
	 * Requires an 'locked_on' and 'locked_by' column
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function lock()
	{
		//Prevent lock take over, only an saved and unlocked row and be locked
		if(!$this->isNew() && !$this->locked())
		{
			$this->locked_by = (int) JFactory::getUser()->get('id');
			$this->locked_on = gmdate('Y-m-d H:i:s');
			$this->save();
		}

		return true;
	}

	/**
	 * Unlock a row
	 *
	 * Requires an locked_on and locked_by column to be present in the table
	 *
	 * @return boolean	If successfull return TRUE, otherwise FALSE
	 */
	public function unlock()
	{
		$userid = JFactory::getUser()->get('id');

		//Only an saved row can be unlocked by the user who locked it
		if(!$this->isNew() && $this->locked_by != 0 && $this->locked_by == $userid)
		{
			$this->locked_by = 0;
			$this->locked_on = 0;

			$this->save();
		}

		return true;
	}

	/**
	 * Checks if a row is locked
	 *
	 * @return boolean	If the row is locked TRUE, otherwise FALSE
	 */
	public function locked()
	{
		$result = false;
		if(!$this->isNew())
		{
		    if(isset($this->locked_on) && isset($this->locked_by))
			{
			    $locked  = strtotime($this->locked_on);
                $current = strtotime(gmdate('Y-m-d H:i:s'));

                //Check if the lock has gone stale
                if($current - $locked < $this->_lifetime)
			    {
                    $userid = JFactory::getUser()->get('id');
			        if($this->locked_by != 0 && $this->locked_by != $userid) {
			            $result= true;
                    }
			    }
			}
		}

		return $result;
	}

	/**
	 * Get the locked information
	 *
	 * @return string	The locked information as an internationalised string
	 */
	public function lockMessage()
	{
		$message = '';

		if($this->locked())
		{
	        $user = JFactory::getUser($this->locked_by);
			$date = $this->getService('com:default.template.helper.date')->humanize(array('date' => $this->locked_on));

			$message = JText::sprintf('Locked by %s %s', $user->get('name'), $date);
		}

		return $message;
	}

	/**
	 * Checks if a row can be updated
	 *
	 * This function determines if a row can be updated based on it's locked_by information.
	 * If a row is locked, and not by the logged in user, the function will return false,
	 * otherwise it will return true
	 *
	 * @return boolean True if row can be updated, false otherwise
	 */
	protected function _beforeTableUpdate(KCommandContext $context)
	{
		return (bool) !$this->locked();
	}

	/**
	 * Checks if a row can be deleted
	 *
	 * This function determines if a row can be deleted based on it's locked_by information.
	 * If a row is locked, and not by the logged in user, the function will return false,
	 * otherwise it will return true
	 *
	 * @return boolean True if row can be deleted, false otherwise
	 */
	protected function _beforeTableDelete(KCommandContext $context)
	{
		return (bool) !$this->locked();
	}
}