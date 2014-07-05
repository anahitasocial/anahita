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
            'request'       => array(
            	'limit'     => 20,
				'sort'		=> 'top',
				'scope' => ''                
            )            
		));
		
		parent::_initialize($config);
	}	

	/**
     * Hides the menubar title
     * {@inheritdoc}
     */
	protected function _actionGet(KCommandContext $context)
	{	  
        $this->getToolbar('menubar')->setTitle(null);   
		return parent::_actionGet($context);
	}
	
	protected function _actionRead(KCommandContext $context)
	{
		$entity = parent::_actionRead($context);
		
		if($this->scope)
		{
			$this->scopes = $this->getService('com://site/components.domain.entityset.scope');
    		$this->current_scope = $this->scopes->find($this->scope);
    		
    		if($this->current_scope)
    			$entity->hashtagables->where('node.type', 'LIKE', '%'.$this->current_scope->identifier);
		}
		
		if($this->sort == 'top')
    		$entity->hashtagables->order('(COALESCE(node.comment_count,0) + COALESCE(node.vote_up_count,0) + COALESCE(node.subscriber_count,0) + COALESCE(node.follower_count,0))', 'DESC');
    	else 
			$entity->hashtagables->order('node.created_on', 'DESC');

		$entity->hashtagables->limit($this->limit, $this->start);	
			
		//print str_replace('#_', 'jos', $entity->hashtagables->getQuery()).'<br>';	
		
		return $entity;
	}
}