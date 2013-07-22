<?php
/**
 * @version 	$Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Controller
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Abstract Controller Behavior
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage 	Behavior
 */
abstract class KControllerBehaviorAbstract extends KBehaviorAbstract
{
	/**
	 * Command handler
	 * 
	 * This function transmlated the command name to a command handler function of 
	 * the format '_before[Command]' or '_after[Command]. Command handler
	 * functions should be declared protected.
	 * 
	 * @param 	string  	The command name
	 * @param 	object   	The command context
	 * @return 	boolean		Can return both true or false.  
	 */
	public function execute( $name, KCommandContext $context) 
	{
		$this->setMixer($context->caller);
		
		$parts  = explode('.', $name);
		if($parts[0] == 'action') 
		{
		    $method = '_action'.ucfirst($parts[1]);
		
		    if(method_exists($this, $method)) {
			    return $this->$method($context);
		    }
		}
		
		return parent::execute($name, $context);
	}
}