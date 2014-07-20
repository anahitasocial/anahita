<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtag
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Hashtag Controller
 *
 * @category   Anahita
 * @package    Com_Hashtag
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsControllerHashtag extends ComBaseControllerService
{	
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(		
            'request' => array(
            	'scope' => '',
				'sort'	=> 'trending',
				'days'	=> KRequest::get('get.days', 'int', 1)                
            )            
		));
		
		parent::_initialize($config);
	}	
	
	protected function _actionRead(KCommandContext $context)
	{
		$entity = parent::_actionRead($context);

		$this->getToolbar('menubar')->setTitle(sprintf(JText::_('COM-HASHTAGS-HEADER-HASHTAG'), $entity->name));
		
		if($this->scope)
		{
			$this->scopes = $this->getService('com://site/components.domain.entityset.scope');
    		if($this->current_scope = $this->scopes->find($this->scope))
    			$entity->hashtagables->where('node.type', 'LIKE', '%'.$this->current_scope->identifier);
		}
		
		if($this->sort == 'top')
    		$entity->hashtagables->order('(COALESCE(node.comment_count,0) + COALESCE(node.vote_up_count,0) + COALESCE(node.subscriber_count,0) + COALESCE(node.follower_count,0))', 'DESC')->groupby('hashtagable.id');
    	else 
			$entity->hashtagables->order('node.created_on', 'DESC');

		$entity->hashtagables->limit($this->limit, $this->start);
		
		return $entity;
	}
	
	/**
	 * Applies the browse sorting 
	 * 
	 * @param KCommandContext $context
	 */
	protected function _actionBrowse(KCommandContext $context)
	{
		if(!$context->query) 
        {
            $context->query = $this->getRepository()->getQuery(); 
        }
		
        $query = $context->query;
        
		$query->select('COUNT(*) AS count')
		->join('RIGHT', 'anahita_edges AS edge', 'hashtag.id = edge.node_a_id')
		->where('edge.type', 'LIKE', '%com:hashtags.domain.entity.association')
		->group('hashtag.id')
		->order('count', 'DESC')
		->limit($this->limit, $this->start);
		
		if($this->sort == 'trending')
		{
			$now = new KDate();			
			$query->where('edge.created_on', '>', $now->addDays(- (int) $this->days)->getDate());
		}
		
		return $this->getState()->setList($query->toEntityset())->getList();
	}
}