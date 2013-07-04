<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Taggable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComBaseDomainBehaviorTaggable extends AnDomainBehaviorAbstract
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
                'tagCount' => array('default'=>0,'write'=>'private'),
                'tagIds'   => array('type'=>'set', 'default'=>'set','write'=>'private')            
            ),
            'relationships' => array(
                'tags' => array(
                    'parent_delete' => 'ignore',
                    'through'   => 'com:tags.domain.entity.association',                    
                    'child_key' => 'taggable',
                    'target'    => 'com:base.domain.entity.node',
                    'target_child_key' => 'tag'
                )
            ),
        ));
        
        parent::_initialize($config);
    }
        
    /**
     * Seaches for hashtags to add/remove a tag
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _beforeEntityInsert(KCommandContext $context)
    {
        $this->_mixer->addHashtagsAsTagsFrom($this->_mixer->body);                
    }
    
    /**
     * Seaches for hashtags to add/remove a tag
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        $this->_mixer->addHashtagsAsTagsFrom($this->_mixer->body);     
    }
    
    
    /**
     * Add an entity hash tags as tags
     * 
     * @param string Extract hashtags from a text and add them as tags to 
     * the entity
     * 
     * @return void
     */
    public function addHashtagsAsTagsFrom($text)
    {
        $regx = '/#(\w+)/';
        $matches = array();
        if ( preg_match_all($regx, $text, $matches) ) {
            $hashtags = array_unique($matches[1]);
            foreach($matches[1] as $tag) {
                $this->_mixer->addTag($tag);
            }
        }
    }
        
    /**
     * Seaches for hashtags to add/remove a tag
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {
        
    }   
    
    /**
     * Adds a tag object to the mixer entity. A tag object can be a simple 
     * string or another node object
     * 
     * @param mixed tag A tag. Can be a simple string or an object
     * 
     * @return void
     */
    public function addTag($tag)
    { 
        if ( is_string($tag) ) {
           //find a text tag
           $tag = $this->getService('repos://site/tags.text')->findOrAddNew(array('name'=>$tag));
        }

        //will not insert the tag if already inserted
        $this->tags->insert($tag);
        return $this;        
    }
    
    /**
     * Change the query to include name. 
     * 
     * Since the target is a simple node. The name field is not included. By ovewriting the
     * tags method we can change the query to include name in the $taggable->tags query
     * 
     * @return AnDomainEntitySet
     */
    public function getTags()
    {
        $this->get('tags')->getQuery()->select('name');
        return $this->get('tags');
    }
    
    /**
     * Removes a tag object to the mixer entity. A tag object can be a simple 
     * string or another node object
     * 
     * @param mixed tag A tag. Can be a simple string or an object
     * 
     * @return void
     */
    public function removeTag($tag)
    {  
        if ( is_string($tag) ) {
           //find a text tag
           $tag = $this->getService('repos://site/tags.text')->find(array('name'=>$tag));
        }
        
        //removes the tag
        if ( $tag ) {                       
            $this->tags->extract($tag);
        }
        
        return $this;
    }
    
    /**
     * Reset subscriptions stats
     *
     * @param array $entities
     * 
     * @return void
     */
    public function resetStats(array $entities)
    {
        foreach($entities as $entity)
        {            
            $ids = $entity->tags->getQuery()->disableChain()->fetchValues('id');            
            $entity->set('tagCount',     count($ids));
            $entity->set('tagIds',       AnDomainAttribute::getInstance('set')->setData($ids));
        }
    }    
 }