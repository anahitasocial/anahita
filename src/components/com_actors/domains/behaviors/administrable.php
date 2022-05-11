<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * An Administrable actor has many one or many admins. Groups, Events are examples
 * of administrable actors that are adminisrated by people.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsDomainBehaviorAdministrable extends AnDomainBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'administratorIds' => array(
                        'type' => 'set', 
                        'default' => 'set', 
                        'write' => 'private'
                    ),
                ),
                'relationships' => array(
                    'administrators' => array(
                        'parent_delete' => 'ignore',
                        'through' => 'com:actors.domain.entity.administrator',
                        'target' => 'com:people.domain.entity.person',
                        'child_key' => 'administrable',
                    ),
                ),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a person as root administrator.
     *
     * @param ComPeopleDomainEntityPerson $person Administrator
     */
    public function addAdministrator($person)
    {
        $result = $this->administrators->insert($person);

        //if the actor is followable then the admin must also follow the actor
        if ($this->isFollowable()) {
            $this->addFollower($person);
        }

        return $result;
    }

    /**
     * Removes a person as the node administrator.
     *
     * @param ComPeopleDomainEntityPerson $person Administrator
     */
    public function removeAdministrator($person)
    {
        $result = $this->administrators->extract($person);

        if (!$result) {
            $this->resetStats(array($this->_mixer, $person));
        }

        return $result;
    }

    /**
     * Before inserting an actor, add the author as an amdin.
     *
     * @param AnConfig $config Configuration
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        if ($this->author) {
            $this->addAdministrator($this->author);
        }
    }

    /**
     * Reset vote stats.
     *
     * @param array $entities
     */
    public function resetStats(array $entities)
    {
        foreach ($entities as $entity) {
            if ($entity->isAdministrable()) {
                $ids = $this->getService('repos:actors.administrator')->getQuery()->administrable($entity)->disableChain()->fetchValues('administrator.id');
                $entity->set('administratorIds', AnDomainAttribute::getInstance('set')->setData($ids));
            } elseif ($entity->isAdministrator()) {
                $ids = $this->getService('repos:actors.administrator')->getQuery()->administrator($entity)->disableChain()->fetchValues('administrable.id');
                $entity->set('administratingIds', AnDomainAttribute::getInstance('set')->setData($ids));
            }
        }
    }

    /**
     * Return an entity set of suitable admin canditates.
     *
     * @return AnDomainEntitsetDefault
     */
    public function getAdminCanditates()
    {
        $viewer = get_viewer();
        $query = $this->getService('repos:people.person')->getQuery();

        if (!$viewer->admin()) {
            $ids = $this->_mixer->followerIds->toArray();
            $ids = array_merge($ids, $viewer->followerIds->toArray());
            $query->id($ids)->id($viewer->id, '<>');
        }
        
        $query->id($this->administratorIds->toArray(), '<>');

        return $query->toEntitySet();
    }
    
    /**
     * Return true if mixer is being administered by the person else return false.
     *
     * @param ComPeopleDomainEntityPeople $actor The actor to block
     *
     * @return bool
     */
    public function hasAdmin($person)
    {
        return $this->_mixer->administratorIds->offsetExists($person->id);
    }
}
