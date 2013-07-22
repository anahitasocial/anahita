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
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story Entity
 *   
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainEntityStory extends ComMediumDomainEntityMedium 
{
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'attributes' => array(
				'name' => array('required'=>true),
			    'body' => array('format'=>'string')
			),
			'relationships'	   => array(
				'subject' => array('required'=>true,'parent'=>'com:actors.domain.entity.actor', 'child_column'=>'story_subject_id'),
				'target'  => array('parent'=>'com:actors.domain.entity.actor',  'child_column'=>'story_target_id'),
				'comment' => array('parent'=>'com:base.domain.entity.comment',  'child_column'=>'story_comment_id'),
				'object'	 => array('polymorphic'=>true, 'type_column'=>'story_object_type', 'child_column'=>'story_object_id', 'parent'=>'com:medium.domain.entity.medium')
			 ),
             'behaviors' => array('commentable'=>array('comment'=>array('format'=>'string')))
		));
						
		parent::_initialize($config);		
	}
	
	/**
	 * Aggregated IDs
	 *
	 * @return array
	 */
	protected $_cache;	
	
	/**
	 * Return an array of aggregated IDs
	 * 
	 * @param  string
	 * @return array 
	 */
	public function getIds($key = null)
	{		
		$prop = $key ? $key : 'id';
		$key  = $key ? $key.'_ids' : 'ids';
		
		if ( !isset($this->_cache[$key]) ) 
		{
			$columns = $this->getRowData();
			
			if ( !empty($columns[$key]) ) 		
				$ids = explode(',',$columns[$key]);
			else {
				$ids = isset($this->$prop) ? ($prop == 'id' ? array($this->id) : array($this->$prop->id)) : array();
			}
						
			$this->_cache[$key] = $ids;
		}
		return $this->_cache[$key];
	}
	
	/**
	 * If the story has an object, it will return the number of comments of the object
	 * 
	 * @return int
	 */
	public function getNumOfComments()
	{
		if ( count($this->getIds('object')) > 1 ) 
			return 0;
			
		if ( !empty($this->object) && $this->object->isCommentable() )
			return $this->object->numOfComments;
		
		return $this->get('numOfComments');
	}

	/**
	 * Overload the comments property to return the object comments if the story
	 * has an object and it's commentable
	 * 
	 * @return int
	 */
	public function getComments()
	{
		if ( !empty($this->object) && $this->object->isCommentable() ) {
			return $this->object->comments;
		}
		
		return $this->get('comments');		
	}	

	/**
	 * Return if a story is an aggregation of multiple stories
	 * 
	 * @return boolean
	 */
	public function aggregated()
	{
		return count($this->getIds()) > 0;
	}
	
	/**
	 * Return if a story has one object
	 * 
	 * @return boolea
	 */
	public function hasObject()
	{
		return !empty($this->object);
	}
	
	/**
	 * Return the voteup count
	 *
	 * @return int
	 */
	public function getVoteUpCount()
	{
	    if ( $this->hasObject() && !is_array($this->object) ) {
	        return $this->object->voteUpCount;
	    }
        return $this->get('voteUpCount');
	}
	
	/**
	 * Return an array of ids of voter
	 *
	 * @return int
	 */
	public function getVoterUpIds()
	{
	    if ( $this->hasObject() && !is_array($this->object) ) {
	        return $this->object->voterUpIds;
	    }
	    return $this->get('voterUpIds');	
	}	
	
	/**
	 * If the object exists, the story URL is the object URL
	 * 	 
	 * @return string
	 */
	public function getURL()
	{
		if ( $this->hasObject() ) {
			return $this->object->getURL();
		} else
			return $this->getStoryURL();	
	}
	
	/**
	 * Return the story URL
	 * 
	 * @param  boolean $aggregate	 
	 * @return string
	 */
	public function getStoryURL($aggregate = false)
	{
		$ids 	= $this->getIds();
		
		if ( $aggregate && count($ids) > 1) 
		{
			$query = array();
			foreach($ids as $id) {
				$query[] = 'id[]='.$id;
			}
			$query = implode('&', $query);
		}
		else $query = 'id='.$this->id;
		
		return 'option=com_stories&view=story&'.$query;		
	}
	
	/**
	 * Overrload the get data to use preloaded entities first
	 * 
	 * @see AnDomainEntityAbstract::getData()
	 */
	public function getData($property = null, $default = null)
	{
		$preloaded_nodes = $this->getRepository()->getLoadedNodes();
		
		if ( $preloaded_nodes && in_array($property, array('subject', 'object', 'target')) ) 
		{
			$key = $property;
			if ( !isset($this->_cache[$key]) )
			{
				$nodes = array();
				$ids   = $this->getIds($property) ;
				foreach($ids as $id) {
					if ( isset($preloaded_nodes[$id]) ) {	
						$nodes[] = $preloaded_nodes[$id];
					}
				}
				if ( count($nodes) < 2 ) {
					$nodes = array_pop($nodes);
				}
				$this->_cache[$key]	 = $nodes;
			}

			return $this->_cache[$key];
		}
		return parent::getData($property, $default);
	}
	
	/**
	 * Called after a comment is inserted
	 * 
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	protected function _afterEntityComment(KCommandContext $context)
	{
		$this->timestamp();
	}

	/**
	 * Called after a vote is inserted
	 * 
	 * @param  KCommandContext $context
	 * @return boolean
	 */
	protected function _afterEntityVoteup(KCommandContext $context)
	{
		$this->timestamp();
	}	
}