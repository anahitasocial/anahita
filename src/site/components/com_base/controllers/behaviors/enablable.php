<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.GetAnahita.com
 */

/**
 * Enablable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerBehaviorEnablable extends KControllerBehaviorAbstract
{
	/**
	 * Enable Entity
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionEnable($context)
	{
	    $context->response->status = KHttpResponse::RESET_CONTENT;
        $this->getItem()->enable();
        return;
	}
	
	/**
	 * Disable Entity
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _actionDisable($context)
	{
	    $context->response->status = KHttpResponse::RESET_CONTENT;
		$this->getItem()->disable();
        return;		
	}
    
    /**
     * Authorize enable
     * 
     * @return boolean
     */
    public function canEnable()
    {
        return $this->canEdit();
    }
    
    /**
     * Authorize disable
     * 
     * @return boolean
     */
    public function canDisable()
    {
        return $this->canEdit();
    }
}