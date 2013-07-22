<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback('after.commit', array($this, 'performCleanup'));
    }    
    
    /**
     * Performs a cleanup after destoryed nodes by deleting any possible related
     * nodes
     *
     * @param KCommandContext $context
     * 
     * @return void
     */
    public function performCleanup(KCommandContext $context)
    {
        $entities = $context->entities;
        $ids       = array();
        $actor_ids = array();
        foreach($entities as $entity)
        {
            if ( $entity->state() ==  AnDomain::STATE_DESTROYED )
            {
                if ( is($entity, 'ComBaseDomainEntityNode') )
                    $ids[] = $entity->id;
            }
        }
        
        $this->cleanupNodesWithIds($ids);
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
                $this->cleanupNodesWithIds($ids);
            }
        }
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
        if ( !empty($ids) )
        {
            //get all the nodes ids whose parent/owner/object/target/comment/subject is one of the deleted nodes
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
            $nids  = $this->getService('repos:base.node')->getQuery()->disableChain()
            ->where($cols)->fetchValues('id');
        
            if ( !empty($nids) )
            {
                //if there are any medium nodes in the related nodes
                //check if they are mentioned in any stories
                $story_ids   = $this->getService('repos:stories.story')->getQuery()->disableChain()->where(array('object.id'=>$nids))->fetchValues('id');
        
                //if there are any stories, queue them to be deleted
                if ( !empty($story_ids) ) {
                    //merge the commment ids to be deleted
                    $nids = array_merge($nids, $story_ids);
                }
        
                //if there are any medium nodes in the related nodes
                //check if they have any comments
                $comment_ids = $this->getService('repos:base.comment')->getQuery()->disableChain()->where(array('parent.id'=>$nids))->fetchValues('id');
        
                //if there are any comments, queue them to be deleted
                if ( !empty($comment_ids) ) {
                    //merge the commment ids to be deleted
                    $nids = array_merge($nids, $comment_ids);
                }
        
                //delete all the nodes
                $this->getService('repos:base.node')->destroy($nids);
        
                //cleanup any relationship any of the deleted nodes may have had
                $ids   = array_merge($ids, $nids);
            }
        
            //if set the last_commentor, created_by, modified_by, last_comment_id to NULL
            //if th nodes are being deleted
            if ( !empty($ids) )
            {
                $in      = '('.implode(',', $ids).')';
                $query[] = "last_comment_id = IF(last_comment_id IN $in, NULL, last_comment_id)";
                $query[] = "created_by = IF(created_by IN $in, NULL, created_by)";
                $query[] = "modified_by = IF(modified_by IN $in, NULL, modified_by)";
                $query[] = "last_comment_by = IF(last_comment_by IN $in, NULL, last_comment_by)";
                $query   = implode(', ', $query);
                $query   = $this->getService('repos:base.node')->getQuery()
                ->where("last_comment_by IN $in OR created_by IN $in OR modified_by IN $in OR last_comment_by IN $in")
                ->update($query)
                ;
                $this->getService('repos:base.node')->getStore()->execute($query);
            }
        
            //cleanup all the relationship of every deleted node
            $query = $this->getService('repos:base.edge')->getQuery()->disableChain()->where('nodeA.id','IN',$ids)->where('nodeB.id','IN',$ids,'OR');
        
            $this->getService('repos:base.edge')->destroy($query);
        
            global $affected_node_ids;
        
            $affected_node_ids = $ids;
        
            $this->getService('anahita:event.dispatcher')->dispatchEvent('onDestroyNodes',
                    array('affected_node_ids'=>$affected_node_ids));
        }
    }
}