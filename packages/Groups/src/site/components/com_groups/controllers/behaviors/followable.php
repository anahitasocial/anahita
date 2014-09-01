<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Groups
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Followable Behavior
 *
 * @category   Anahita
 * @package    Com_Groups
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComGroupsControllerBehaviorFollowable extends ComActorsControllerBehaviorFollowable
{               
	/**
	 * Add $data->actor to the current actor resource. status is set to 
     * KHttpResponse::RESET_CONTENT;
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void 
	 */
	protected function _actionAddfollower(KCommandContext $context)
	{
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
		if(!$this->getItem()->leading($this->actor))
		{
		    $this->getItem()->addFollower($this->actor);
		   
		    $story = $this->createStory(array(
		    		'name' => 'actor_follow',
		        	'subject' => $this->actor,
		        	'owner' => $this->getItem(),
		        	'target' => $this->getItem()
		    	));
		    
		    if($this->viewer->eql($this->actor))
		    {
		    	$this->createNotification(array(
		    		'name' => 'actor_follow',
		    		'subject' => $this->actor, 
		    		'target' => $this->getItem()
		    	));
		    }
		    else 
		    { 
		    	$this->createNotification(array(
		    		'name' => 'actor_leadable_add',
		    		'subject' => $this->viewer,
		    		'target' => $this->getItem(),
		    		'object' => $this->actor,
		    		'subscribers' => array($this->actor->id)
		    	));
		    }
		}
        
        return $this->getItem();
	}
}
