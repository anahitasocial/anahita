<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Domain Space. Implements unit of work and domain entitis states
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainSpace extends AnDomainSpace implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
        
        return $container->get($config->service_identifier);
    }    
    
    /**
     * After a succesfull commit perform a cleanup of nodes
     * 
     * @param mixed &$failed Return the failed set
     * 
     * @return boolean Return if all the entities passed the validations
     */
    public function commitEntities(&$failed = null)
    {
        if ( $ret = parent::commitEntities($failed) )
        {
            $ids  = array();
             
            foreach($this->_entities as $entity)
            {    
                if ( $entity->getEntityState() ==  AnDomain::STATE_DESTROYED )
                {
                    if ( is($entity, 'ComBaseDomainEntityNode') )
                        $ids[] = $entity->id;
                }
            }
            
            if ( !empty($ids) ) {
                $this->cleanupNodesWithIds($ids);
            }
        }
        
        return $ret;
    }
    
    /**
     * Remove node, edges for the passed components
     * 
     * @param array $components an array of components names in format of com_[name]
     * 
     * @return void
     */
    public function removeNodesWithComponent($components)
    {
        settype($components, 'array');
     
        if ( count($components) )
        {
            //get all the nodes belonging to the components   
            $ids = $this->getService('repos:base.node')
                        ->getQuery()
                        ->disableChain()
                        ->component($components)
                        ->fetchValues('id');
         
            if ( count($ids) )
            {
                //delete all the nodes
                $this->getService('repos:base.node')->destroy($ids);
                        
                //clean up any related nodes
                if ( !empty($ids) ) {
                    $this->cleanupNodesWithIds($ids);
                }                
            }
        }
    }

    /**
     * Get an array of related nodes ids 
     * 
     * @param array $ids An array of ids
     * 
     * @return array
     */
    public function getRelatedNodeIds($ids)
    {
        $cols = array(
                'story_object_id' ,
                'story_subject_id',
                'story_comment_id',
                'story_target_id' ,
                'parent_id',
                'owner_id'
        );
        
        foreach($cols as $key => $value) {
            $cols[$key] = $value.' IN ('.implode(',',$ids).')';
        }
        
        $cols  = implode(' OR ', $cols);
        
        //all the nodes ids that are somehow related to deleted nodes
        //possible objects (comments/stories/medium nodes)
        $nids  = $this->getService('repos:base.node')
                    ->getQuery()
                    ->disableChain()
                    ->where($cols)->fetchValues('id');

        if ( !empty($nids) ) 
        {
            $story_ids   = $this->getService('repos:stories.story')->getQuery()->disableChain()->where(array('object.id'=>$nids))->fetchValues('id');            
            $nids        = array_merge($nids, $story_ids);
            
            //if there are any medium nodes in the related nodes
            //check if they have any comments
            $comment_ids = $this->getService('repos:base.comment')->getQuery()->disableChain()->where(array('parent.id'=>$nids))->fetchValues('id');            
            $nids        = array_merge($nids, $comment_ids);
        }
        
        return array_unique($nids);
    }
    
    /**
     * Return an array of node repositories for a list of ids
     * 
     * @param array $ids
     * 
     * @return array
     */
    public function getNodeRepositories($ids)
    {
        $types = $this->getService('repos:base.node')
                ->getQuery()
                ->disableChain()
                ->id($ids);
        
        $types->distinct = true;
        $repositories    = array();
        foreach($types->fetchValues('type') as $key => $type)
        {
            $type = explode(',',$type);
        	$identifier = end($type);
            $repositories[] = AnDomain::getRepository($identifier);            
        }
        return $repositories;
    }
    
    /**
     * Clean up node, edge table for the deleted nodes with passed ids
     * 
     * @param array $ids Deleted node ids
     * 
     * @return void
     */
    public function cleanupNodesWithIds($ids)
    {
        $ids = array_merge($this->getRelatedNodeIds($ids), $ids);
        
        if ( !empty($ids) )
        {
            $repositories = $this->getNodeRepositories($ids);
            
            //delete all the nodes
            $this->getService('repos:base.node')->destroy($ids);
            
            //set the creator,updator,authors to null
            $in      = '('.implode(',', $ids).')';
            $query[] = "last_comment_id = IF(last_comment_id IN $in, NULL, last_comment_id)";
            $query[] = "created_by = IF(created_by IN $in, NULL, created_by)";
            $query[] = "modified_by = IF(modified_by IN $in, NULL, modified_by)";
            $query[] = "last_comment_by = IF(last_comment_by IN $in, NULL, last_comment_by)";
            $query   = implode(', ', $query);
            $where   = $this->getService('repos:base.node')
                        ->getQuery()
                        ->where("last_comment_by IN $in OR created_by IN $in OR modified_by IN $in OR last_comment_by IN $in");
            
            $this->getService('repos:base.node')->update($query, $where);
            
            //cleanup all the relationship of every deleted node
            $query = $this->getService('repos:base.edge')
                ->getQuery()
                ->where('nodeA.id','IN',$ids)
                ->where('nodeB.id','IN',$ids,'OR');
            
            $this->getService('repos:base.edge')->destroy($query);
            
            //now lets the repositories know so they can
            //handle the cleanup
            foreach($repositories as $repository)
            {
                $context = $repository->getCommandContext();
                $context->node_ids = $ids;
                $repository->getCommandChain()->run('after.deletenodes', $context);
            }            
        }        
    }
}