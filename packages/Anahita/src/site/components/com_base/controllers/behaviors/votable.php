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
 * @link       http://www.anahitapolis.com
 */

/**
 * Votable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorVotable extends KControllerBehaviorAbstract 
{
	/**
	 * Renders ComBaseTemplateHelperUi::vote()
	 *
	 * @param  KCommandContext $context Context parameter
     * 
	 * @return string
	 */	
	protected function _actionGetvoters($context)
	{
		$this->commit();
        
		if ( $context->request->getFormat() == 'html' ) 
        {
            return $this->getView()
                ->getTemplate()
                ->renderHelper('ui.voters', $this->getItem(), array('avatars'=>$this->avatars));			
        } else {
            
            $voters     = $this->getItem()->voteups->voter;
            $controller = $this->getService('com://site/actors.controller.actor')
                             ->view('actors')
                             ->format($context->request->getFormat()); 

            $controller->getState()->setList($voters);
            return $controller->getView()->display();            
        }
	}
		
	/**
	 * Subscribe the viewer to the subscribable object
	 *
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionVote($context)
	{				
	    $context->response->status = KHttpResponse::CREATED;
	    
		$this->getItem()->voteup( get_viewer() );
		$notification = $this->_mixer->createNotification(array(
			'name' 		=> 'voteup',
			'object'	=> $this->getItem(),
		    'component' => $this->getItem()->component
		));
		$context->response->content = 
		    $this->_mixer->execute('getvoters', $context);
	}
	
	/**
	 * Remove the viewer's subscription from the subscribable object
	 *
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionUnvote($context)
	{
		$this->getItem()->unvote( get_viewer() );
		$context->response->content = 
		    $this->_mixer->execute('getvoters', $context);
	}
}

?>