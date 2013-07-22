<?php
/**
 * @version 	$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Abstract Database Behavior
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 * @subpackage 	Behavior
 */
abstract class KDatabaseBehaviorAbstract extends KBehaviorAbstract
{
	/**
	 * Command handler
	 * 
	 * This function translates the command name to a command handler function of the format '_before[Command]' or
     * '_after[Command]. Command handler functions should be declared protected.
	 * 
	 * @param 	string  	The command name
	 * @param 	object   	The command context
	 * @return 	boolean		Can return both true or false.  
	 */
	public function execute( $name, KCommandContext $context) 
	{
	    if($context->data instanceof KDatabaseRowInterface) {
	        $this->setMixer($context->data);
		}
		
		return parent::execute($name, $context);
	}
	
	/**
     * Saves the row or rowset in the database.
     *
     * This function specialises the KDatabaseRow or KDatabaseRowset save function and auto-disables the tables command
     * chain to prevent recursive looping.
     *
     * @return KDatabaseRowAbstract or KDatabaseRowsetAbstract
     * @see KDatabaseRow::save or KDatabaseRowset::save
     */
    public function save()
    {
        //Clone the mixer to prevent status changes
        $mixer = clone $this->getMixer();

        $mixer->getTable()->getCommandChain()->disable();
        $mixer->save();
        
        return $this->_mixer;
    }
    
    /**
     * Deletes the row form the database.
     * 
     * This function specialises the KDatabaseRow or KDatabaseRowset delete function and auto-disables the tables
     * command chain to prevent recursive looping.
     *
     * @return KDatabaseRowAbstract
     */
    public function delete()
    {
        //Clone the mixer to prevent status changes
        $mixer = clone $this->getMixer();

        $mixer->getTable()->getCommandChain()->disable();
        $mixer->delete();
        
        return $this->_mixer;
    }
    
    /**
     * Get the methods that are available for mixin based 
     * 
     * This function also dynamically adds a function of format is[Behavior] to allow client code to check if the
     * behavior is callable.
     * 
     * @param object The mixer requesting the mixable methods. 
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = parent::getMixableMethods($mixer);       
        return array_diff($methods, array('save', 'delete'));
    }
}