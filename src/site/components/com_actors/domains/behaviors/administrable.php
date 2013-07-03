<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * An Administrable actor has many one or many admins. Groups, Events are examples
 * of administrable actors that are adminisrated by people
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorAdministrable extends AnDomainBehaviorAbstract
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
                'attributes'	=> array(
                        'administratorIds' => array('type'=>'set', 'default'=>'set', 'write'=>'private')
                ),
                'relationships' => array(
                        'administrators' => array(
                                'parent_delete' => 'ignore',
                                'through' 	=> 'com:actors.domain.entity.administrator',
                                'target'	=> 'com:people.domain.entity.person',
                                'child_key' => 'administrable'
                        )
                )
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Adds a person as root administrator
     *
     * @param ComPeopleDomainEntityPerson $person Administrator
     *
     * @return void
     */
    public function addAdministrator($person)
    {
        $result = $this->administrators->insert($person);
        //if the actor is followable then the admin must also follow
        //the actor
        if ( $this->isFollowable() ) {
            $this->addFollower($person);
        }
        return $result;
    }
    
    /**
     * Removes a person as the node administrator
     *
     * @param  ComPeopleDomainEntityPerson $person Administrator
     *
     * @return void
     */
    public function removeAdministrator($person)
    {
        $result = $this->administrators->extract($person);
    
        if ( !$result ) {
            $this->resetStats(array($this->_mixer, $person));
        }
        return $result;
    }
    
    /**
     * Before inserting an actor, add the author as an amdin
     *
     * @param KConfig $config Configuration
     *
     * @return  void
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        if ( $this->author )
            $this->addAdministrator($this->author);
    }
        
    /**
     * Reset vote stats
     *
     * @param array $entities
     * 
     * @return void
     */
    public function resetStats(array $entities)
    {
        foreach($entities as $entity)
        {
            if ( $entity->isAdministrable() )
            {
                $ids = $this->getService('repos:actors.administrator')->getQuery()->administrable($entity)->disableChain()->fetchValues('administrator.id');
                $entity->set('administratorIds', AnDomainAttribute::getInstance('set')->setData($ids));
            } 
            elseif ( $entity->isAdministrator() )
            {
                $ids    = $this->getService('repos:actors.administrator')->getQuery()->administrator($entity)->disableChain()->fetchValues('administrable.id');               
                $entity->set('administratingIds', AnDomainAttribute::getInstance('set')->setData($ids));                             
            }
        }
    }
        
    /**
     * Return an entity set of suitable admin canditates
     *
     * @return AnDomainEntitsetDefault
     */
	public function getAdminCanditates()
	{
	    $viewer = get_viewer();
	    $query	= $this->getService('repos://site/people.person')->getQuery();
	    
	    //super admin can make anyone admin
	    if ( !$viewer->admin() )
	    {
	        $ids    = $this->_mixer->followerIds->toArray();
	        $ids	= array_merge($ids, $viewer->followerIds->toArray());
	        $query->id($ids)->id($viewer->id,'<>')->id($this->administratorIds->toArray(),'<>');	        
	    }
	    	    	    
	    return $query->toEntitySet();
	}
}