<?php
/**
 * @version		$Id: discoverable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Controller Discoverable Command Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage	Behavior
 */
class KControllerBehaviorDiscoverable extends KControllerBehaviorAbstract
{
	/**
	 * Get a list of allowed actions
	 *
     * @return  string    The allowed actions; e.g., `GET, POST [add, edit, cancel, save], PUT, DELETE`
	 */
	protected function _actionOptions(KCommandContext $context)
	{
	    $methods = array();

        //Remove GET actions
        $actions = array_diff($this->getActions(), array('browse', 'read', 'display'));

        //Authorize the action
        foreach($actions as $key => $action)
        {
            //Find the mapped action if one exists
            if (isset( $this->_action_map[$action] )) {
                $action = $this->_action_map[$action];
            }

            //Check if the action can be executed
            if($this->getBehavior('executable')->execute('before.'.$action, $context) === false) {
                unset($actions[$key]);
            }
        }

        //Sort the action alphabetically.
        sort($actions);

        //Retrieve HTTP methods
        foreach(array('get', 'put', 'delete', 'post', 'options') as $method)
        {
            if(in_array($method, $actions)) {
                $methods[strtoupper($method)] = $method;
            }
        }

        //Retrieve POST actions
        if(in_array('post', $methods))
        {
            $actions = array_diff($actions, array('get', 'put', 'delete', 'post', 'options'));
            $methods['POST'] = array_diff($actions, $methods);
        }

        //Render to string
        $result = implode(', ', array_keys($methods));

        foreach($methods as $method => $actions)
        {
           if(is_array($actions) && !empty($actions)) {
               $result = str_replace($method, $method.' ['.implode(', ', $actions).']', $result);
           }
        }

        $context->headers = array('Allow' => $result);
	}
}