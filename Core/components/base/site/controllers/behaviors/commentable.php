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
	    if ( $this->cid )
        {
	        $context->response->content 
	            = $this->getCommentController()->id($this->cid)->display();
	        return false;      
	    }
        elseif ($this->permalink && !$context->request->isAjax() ) 
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
		   $context->response->setRedirect($url.'#scroll='.$this->permalink);		   			
           return false;
		}
	}
	
	/**
	 * Render the comments
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	protected function _actionGetcomments(KCommandContext $context)
	{
	    $this->getCommentController()
	        ->getRequest()->remove('get');
	    
        $this->getCommentController()
                ->limit($this->getRequest()->get('limit'))
                ->start($this->getRequest()->get('start'))
                ;
	    
	    $this->getCommentController()
	        ->view('comments')
	        ->execute('get', $context);
	}
	
	/**
	 * Adds a comment
	 *
	 * @param KCommandContext $context
	 *
	 * @return ComBaseDomainEntityComment
	 */
	protected function _actionDeletecomment(KCommandContext $context)
	{
        $ret = $this->getCommentController()->id($this->cid)->delete();
	    $context->response->status  = KHttpResponse::NO_CONTENT;	    
	}
		
	/**
	 * Adds a comment
	 *
	 * @param KCommandContext $context
	 *
	 * @return ComBaseDomainEntityComment
	 */
	protected function _actionEditcomment(KCommandContext $context)
	{    
	    $data    = $context->data;
        $comment = $this->getCommentController()->id($this->cid)->edit(array('body'=>$data->body));        
	    $context->comment = $comment;	    
	    if ( $this->isDispatched() ) {
	        $context->response->content = $this->getCommentController()->display();
	    }
	    return $comment;     
	}
		
	/**
	 * Adds a comment
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return ComBaseDomainEntityComment
	 */
	protected function _actionAddcomment(KCommandContext $context)
	{	    
        $data    = $context->data;
	    $comment = $this->getCommentController()->add(array('body'=>$data->body));
	    $context->response->status = KHttpResponse::CREATED;
	    $context->comment = $comment;	   
	    if ( $this->isDispatched() ) 
	    {
	        $context->response->content = $this->getCommentController()->display();
	        if ( $context->request->getFormat() == 'html' ) 
	        {
	            $offset = $this->getItem()->getCommentOffset( $comment->id );
	            $start  = (int)($offset / $this->limit) * $this->limit;
	            $context->response->setRedirect(JRoute::_($comment->parent->getURL().'&start='.$start).'#scroll='.$comment->id);
	        }
	        else {
	            $context->response->setRedirect(JRoute::_($comment->getURL()));
	        }
	    }
	    return $comment;
	}

	/**
	 * Vote on a comment
	 *
	 * @param KCommandContext $context
	 *
	 * @return void
	 */
	protected function _actionUnvoteComment(KCommandContext $context)
	{
	    $this->getCommentController()
	    ->id($this->cid)->execute('unvote', $context);
	}
		
	/**
	 * Vote on a comment
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	protected function _actionVoteComment(KCommandContext $context)
	{
	    $this->getCommentController()
	        ->id($this->cid)->execute('vote', $context);
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
	        $identifier = clone $this->getIdentifier();
	        $identifier->path = array('controller');
	        $identifier->name = 'comment';
	        $request = new LibBaseControllerRequest(array('format'=>$this->getRequest()->getFormat()));
	        if  ( $this->getRequest()->has('get') ) 
	        {
	            $request->set('get', $this->getRequest()->get('get'));
	        }
	        $request->append(pick($this->_mixer->getRequest()->comment,array()));
	        $this->_comment_controller = $this->getService($identifier, array(
                    'request'  => $request,
                    'response' => $this->getResponse()
	        ));
	        //set the parent
	        if ( $this->getItem() ) {
	            $this->_comment_controller->pid($this->getItem()->id);
	        }    
	    }
	    return $this->_comment_controller;
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