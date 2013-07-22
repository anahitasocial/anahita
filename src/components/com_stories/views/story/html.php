<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Renders a list of stories with their comment.
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesViewStoryHtml extends ComBaseViewHtml
{
	/**
	 * Sets the story title, body and avatar
	 * 
	 * @return void
	 */
	protected function _layoutList()
	{	  
	    $preloaded_nodes = $this->getService('repos:stories.story')->getLoadedNodes();
		$story  = $this->_state->getItem();
		$helper = $this->getTemplate()->getHelper('parser');		
		$data   = $helper->parse($story, $this->actor);
	
		$data['subject'] 	= $story->subject;
		$data['timestamp']	= $story->creationTime;
		
		$viewer 			= get_viewer();
		
		//setup the comments
		$comment_ids = $story->getIds('comment');
		$comments	 = array();

		//only shows comments if there are comment_ids in the story
		//body or if the story has directly been commented on
		if ( !empty($comment_ids) ) 
		{
		    sort($comment_ids);
            $size = 0;
		    foreach($comment_ids as $id)
		    {
		        if ( isset($preloaded_nodes[$id]) )
		        {
		            $comment = $preloaded_nodes[$id];
		            if ( $comment instanceof ComBaseDomainEntityComment )
		            {
		                $comments[$id] = $comment;
                        $size++;
		            }
		        }
                if ( $size == 10 )
                    break;
		    }
		}
		elseif ( empty($story->object) && $story->numOfComments > 0 ) 
		{
			$comments = $story->comments->limit(10);
			$comments->order('creationTime', 'DESC');
			$tmp = $comments->getRepository()->fetch($comments->getQuery(), AnDomain::FETCH_ENTITY_LIST);
			$comments->reset();
			$comments = array_reverse($tmp);
		}

 		$data['commands']  = $this->getTemplate()->renderHelper('toolbar.commands', 'list');
		$data['comments']  = $comments;
		
		$this->set($data);
	}
    
    /**
     * Called before default layout
     * 
     * @return void
     */
    protected function _layoutDefault()
    {
        $story  = $this->_state->getItem();        
        $helper = $this->getTemplate()->getHelper('parser');        
        $data   = $helper->parse($story, $this->actor);
    
        $data['subject']    = $story->subject;
        $data['timestamp']  = $story->creationTime;
        $data['story']      = $story;
        $data['entity']     = $story;
        $viewer             = get_viewer();   
        $commands = $this->getTemplate()->renderHelper('toolbar.commands', 'list');
        $commands->offsetUnset('comment'); 
        $this->set($data);    
    }
}