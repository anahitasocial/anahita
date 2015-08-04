<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Tags
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Abstract Tag Controller
 *
 * @category   Anahita
 * @package    Com_Tags
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
abstract class ComTagsControllerAbstract extends ComBaseControllerService
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
	
	/**
	 * Read Service
	 * 
	 * @param KCommandContext $context
	 */
	protected function _actionRead(KCommandContext $context)
	{
		$entity = parent::_actionRead($context);
		
		if($this->scope)
			$entity->tagables->scope($this->scope);
		
		if($this->sort == 'top')
    		$entity->tagables->sortTop();
    	else 
			$entity->tagables->sortRecent();
		
		$entity->tagables->limit($this->limit, $this->start);
		
		//print str_replace('#_', 'jos', $entity->tagables->getQuery());
		
		return $entity;
	}
	
	/**
	 * Browse Service
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
		->join('RIGHT', 'edges AS edge', 'hashtag.id = edge.node_a_id')
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