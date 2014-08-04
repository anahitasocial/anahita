<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Mentionable Behavior
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
 class ComPeopleDomainBehaviorMentionable extends AnDomainBehaviorAbstract
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
            'relationships' => array(
                'mentions' => array(
                    'through' => 'com:people.domain.entity.mention',                    
                    'target' => 'com:base.domain.entity.node',
                    'child_key' => 'mentionable',
                    'target_child_key' => 'mention',
            		'inverse' => true
                )
            )
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Adds a person to a mentionable mixer entity
     * 
     * @param a person username
     * @return void 
     */
    public function addMention($username)
    {    	
    	if($mention = $this->getService('repos://site/people.person')->find(array('username'=>$username)))
    	{
    		$this->mentions->insert($mention);
    		
    		if($this->_mixer->isSubscribable())
				$this->_mixer->addSubscriber($mention);
    		
    		return $this;
    	}
    			
    	return;
    }
    
 	/**
     * Removes a person from a mentionable mixer entity
     * 
     * @param a word
     * @return void 
     */
    public function removeMention($username)
    {    	
    	if($mention = $this->getService('repos://site/people.person')->find(array('username'=>$username)))
    	{
    		$this->mentions->extract($mention);
    		
    		if($this->_mixer->isSubscribable())
				$this->_mixer->removeSubscriber($mention);
    		
    		return $this;
    	}
    			
    	return;
    }
    
 	/**
     * Change the query to include name. 
     * 
     * Since the target is a simple node. The name field is not included. By ovewriting the
     * tags method we can change the query to include name in the $taggable->tags query
     * 
     * @return AnDomainEntitySet
     */
    public function getMentions()
    {
        $this->get('mentions')->getQuery()->select('node.person_username');
        return $this->get('mentions');
    }
 }