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
 * @subpackage Domain_Repository
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story Repository
 *   
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Repository
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainRepositoryStory extends AnDomainRepositoryDefault
{
    /**
     * Loaded nodes
     * 
     * @var array
     */
    protected $_loaded_nodes;
    
	/**
	 * Creates a new story
	 * 
	 * @param  array $data The story data
	 * 
	 * @return ComStoriesDomainEntityStory
	 */	
	public function create($data)
	{
		$data = new KConfig($data);
				
		$data->append(array(
			'owner' 	=> $data->target ? $data->target : $data->subject
		));
		
		$data  = KConfig::unbox($data);
		
		return $this->getEntity(array('data'=>$data));		
	}
	
	/**
	 * Before fetch
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeRepositoryFetch(KCommandContext $context)
	{
	    
		$query = $context->query;
				
		$query->where('IF(@col(target.enabled) IS NULL, 1, @col(target.enabled) <> 0) AND IF(@col(subject.enabled) IS NULL, 1, @col(subject.enabled) <> 0)')
		      ->link('target', array('type'=>'weak','bind_type'=>false))
            ;
        //$days = $query->limit % 20 + 10;
        //$query->where('DATEDIFF(NOW(), @col(updateTime)) < '.$days);
        
//        $date = AnDomainAttributeDate::getInstance()->modify('-1 days');
//        $query->where('updateTime','>',$date->getDate(DATE_FORMAT_UNIXTIME));
                        
		if ( $query->summerize )
			$this->_summerize($query, $query->summerize);
	}

	/**
	 * After fetch
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _afterRepositoryFetch(KCommandContext $context)
	{
		$query = $context->query;
		
		if ( $query->summerize )
			$this->_preload($context->data);
	}
	
	/**
	 * Summerize a story query
	 *
	 * @param AnDomainQuery $query   Query object
	 * @param array         $options Options
	 * 
	 * @return AnDomainEntityset
	 */
	protected function _summerize($query, $options)
	{
		$cases 		 = array();
        
        $options     = KConfig::unbox($options);
        $options[]   = array(
             'WHEN @col(name) LIKE "avatar_edit"  THEN IF(@col(subject.id) = @col(target.id), "", @col(id))',
             'WHEN @col(name) LIKE "actor_follow" THEN @col(subject.id)'    
        );  
   
		foreach($options as $component => $keys) 
		{
			foreach($keys as $name => $key)
			{
                if ( !is_numeric($name) )
                {
                    $names = explode(',', $name);
                    $keys  = explode(',', $key);
                    
                    foreach($keys as $i => $key)
                        $keys[$i]   = '@col('.$key.'.id)';
                    
                    foreach($names as $name)
                    {
                        $cases[]='WHEN CONCAT(@col(name),@col(component)) LIKE \''.$name.$component.'\' THEN CONCAT('.implode(',',$keys).')';
                    }
                } else 
                {
                    $cases[] = $key;    
                }
								
			}
		}

        $cases  = array_unique($cases);
        
		$keys   = array();
		
		//always group by date, name and component
		$date   = '@col(name),@col(component),DATE(@col(creationTime))';
		$keys[]	= $date;
		if ( !empty($cases) )
		{
			$case   = 'CASE TRUE ';
			$case  .= implode(' ',$cases).' ';
			$case  .= 'ELSE CONCAT(@col(target.id),@col(subject.id),@col(object.id)) '; 
			$case  .= 'END';
			$keys[] = $case;
		} else
			$keys[] = '@col(target.id),@col(subject.id),@col(object.id)';
					
		$keys	= implode(',', $keys);
		$comment_if = "IF(@col(comment.id) IS NOT NULL AND @col(object.id) IS NOT NULL , CONCAT_WS(',',$date, @col(object.id)), CONCAT_WS(',',$keys))";
		//don't group if a story has a body or it has directly been commented on
		$bundle = "IF (@col(body) <> '' AND @col(body) IS NOT NULL,@col(id),$comment_if) AS bundle_key";
		$query->select($bundle);
		$query->select('GROUP_CONCAT(DISTINCT @col(id)) AS ids');
		$query->select('GROUP_CONCAT(DISTINCT @col(owner.id))  AS  owner_ids');
		$query->select('GROUP_CONCAT(DISTINCT @col(target.id)) AS   target_ids');
		$query->select('GROUP_CONCAT(DISTINCT @col(comment.id)) AS  comment_ids');
		$query->select('GROUP_CONCAT(DISTINCT @col(object.id)) AS  object_ids');
		$query->select('GROUP_CONCAT(DISTINCT @col(subject.id)) AS subject_ids');
		$query->select(array('id'=>'MAX(@col(id))'));
		$query->select(array('creationTime'=>'MAX(@col(creationTime))'));
		$query->select(array('updateTime'=>'MAX(@col(updateTime))'));
		$viewer = get_viewer();
		/*
		if ( $viewer->id ) {		    
		    $query->where("IF( @col(owner.enabled) IS NULL,1,IF( @col(owner.enabled),1,@col(owner.id) IN %s))", array($viewer->administratingIds->toArray()));		      
		}
		else $query->where("IF(@col(owner.enabled) IS NULL,1,@col(owner.enabled))");
		*/
		$query->group('bundle_key');
		$query->order 	= array();
		$query->order('modified_on', 'DESC');
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function _fetchResult($query, $mode)
	{
	    //$query = 'SELECT wrapper.* FROM ('.$query.') AS wrapper ORDER BY modified_on DESC';
	    return $this->getStore()->fetch($query, $mode);
	}
		
	/**
	 * Loads all the necessary objects after each collection fetch
	 * 
	 * @param  AnDomainEntityset $stories
	 * @return void
	 */
	protected function _preload($stories)
	{
		$node_ids       = array();
		$comment_ids    = array();
		foreach($stories as $story)
		{
			$columns    = $story->getRowData();
			$node_ids	= array_merge($node_ids, 
					$story->getIds('owner'), 
					$story->getIds('subject'), 
					$story->getIds('target'), 
					$story->getIds('object')
			        );
			$comment_ids = array_merge($comment_ids, $story->getIds('comment'));
			
		}
		$node_ids    = array_unique($node_ids);
		$query    	 = $this->getService('repos:base.comment')->getQuery()->where('parent.id','IN',$node_ids);
		$author_ids  = $query->fetchValues('author.id');
		$node_ids    = array_unique(array_merge($node_ids, $author_ids, $comment_ids));
				
		
		//we don't any behavior messes around with the fetched stories				
		$query  = $this->getService('repos:base.node')->getQuery()->id($node_ids)->disableChain();
		$query->columns('*');
		$nodes = $query->fetchSet();
		$nodes = AnHelperArray::indexBy($nodes, 'id');
		$this->_loaded_nodes = $nodes;
	}
	
	/**
	 * Return an array of previously loaded nodes
	 *
	 * @return array
	 */
	public function getLoadedNodes()
	{
	    return $this->_loaded_nodes;
	}
}