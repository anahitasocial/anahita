<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
 * Commentable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorCommentable extends KControllerBehaviorAbstract 
{
	/**
	 * Comment Controller
	 * 
	 * @var ComBaseControllerComment
	 */	
	protected $_comment_controller;	
		
	/**
	 * Intercept a get method to check whether to show the comments only or not
	 *
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	protected function _beforeControllerGet(KCommandContext $context)
	{		
		 if ($this->permalink && KRequest::type() != 'AJAX' ) 
		 {
            $cid	= (int)preg_replace('/[^\d]+/', '', $this->permalink);
			$offset = $this->getItem()->getCommentOffset( $cid );
			$start  = (int)($offset / $this->limit) * $this->limit;
			$url	= KRequest::url();
			$query  = $url->getQuery(true);
			if ( $this->start != $start ) {				
				$query  = array_merge($query, array('start'=>$start));				
			}
			unset($query['permalink']);							
			$url->setQuery($query);
			$this->setRedirect($url.'#scroll='.$this->permalink);
			return;
		} 
	}
	
	/**
	 * Forward an action to the comment controller
	 * 
	 * @param  string $name
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	public function execute($name, KCommandContext $context)
	{		
		$parts = explode('.', $name);
		if ( $parts[0] == 'before' && KRequest::has('request.comment') ) 
		{
			$data 	= KRequest::has('post.comment') ? KRequest::get('post.comment', 'raw') : array();
			$cntx 	= new KCommandContext(array('data'=>$data));
			$action = pick($cntx->data->action, $context->action, KRequest::method());
			$context->result = $this->getCommentController()->execute($action, $cntx);
			if ( $action == 'post' ) {
				$context->result = $this->getCommentController()->display();
			}
			return false;
		}
		return parent::execute($name, $context);
	}		
	
	/**
	 * Returns the comment controller
	 * 
	 * @return ComBaseControllerComment
	 */
	public function getCommentController()
	{
		if ( !isset($this->_comment_controller) ) 
		{
			$request = new KConfig(KRequest::get('get', 'raw'));
			$request->append(array(
				'comment' => array(
					'pid'	 => $request->id,
					'format' => 'html',
					'view'	 => 'comment',
					'layout' => 'list'	 ,
					'get'	 => $request->get
				)
			));
			$request = $request->comment;
			$identifier = clone $this->getIdentifier();
			$identifier->path = array('controller');
			$identifier->name = 'comment';
			register_default(array('identifier'=>$identifier, 'default'=>'ComBaseControllerComment'));
			$this->_comment_controller	 = $this->getService($identifier, array(
				'request' => KConfig::unbox($request) 
			));
			
			$this->_comment_controller->registerCallback('after.add',    array($this->_mixer, 'createCommentStory'));			
		}
		
		return $this->_comment_controller;
	}
	
    /**
     * Callback method called from comment controlller after comment add action to create a 
     * comment story after an object has been commented
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    public function createCommentStory(KCommandContext $context)
	{
        //called by the comment controller as as callback
	    $entity   = $context->caller->getItem();
	    $parent   = $entity->parent;
        
	    //dn't make a comment story for commenting on a story
	    if ( $parent->getIdentifier()->name == 'story' ) {
	        return;
	    }
        
        $owner = $entity->author;
        	    
	    if ( $parent->isOwnable() ) {
	        $owner  = $parent->owner;
	    }
        
	    $data = array(
			'name' 		=> $parent->getIdentifier()->name.'_comment',
			'component'	=> $parent->component,
			'comment'	=> $entity,
			'object'	=> $parent,
			'owner'		=> $owner,
			'target'	=> $parent->isOwnable()  ? $parent->owner : null	    			
	    );
	   
	    $story               = $this->createStory($data);
        
        //story owner	    
	    $data['subscribers'] = array($story->owner);
	    
        //all the not subscribers
	    if ( $parent->isSubscribable() ) {
	        $data['subscribers'][] = $parent->subscriberIds->toArray();
	    }
	    
	    $notification  = $this->createNotification($data);
	    
	    $notification->setType('post');
	    
	    $story->save();
	}
	
	/**
	 * Toggles comment status
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionCommentstatus($context)
	{
		$data = $context->data;
		$this->getItem()->openToComment = (bool)$data->status;
	}		
}

?>