<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Leadable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsDomainBehaviorLeadable extends AnDomainBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'leaderCount' => array('default' => 0, 'write' => 'private'),
                'leaderIds' => array('type' => 'set', 'default' => 'set', 'write' => 'private'),
                'blockerIds' => array('type' => 'set', 'default' => 'set', 'write' => 'private'),
                'mutualIds' => array('type' => 'set', 'default' => 'set', 'write' => 'private'),
                'mutualCount' => array('default' => 0, 'write' => 'private'),
            ),
            'relationships' => array(
                'blockers' => array(
                    'through' => 'com:actors.domain.entity.block',
                    'parent_delete' => 'ignore',
                    'child_key' => 'blocked',
                    'target' => 'com:actors.domain.entity.actor',
                ),
                'leaders' => array(
                    'parent_delete' => 'ignore',
                    'through' => 'com:actors.domain.entity.follow',
                    'child_key' => 'follower',
                    'target' => 'com:actors.domain.entity.actor',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Removes an $actor from the list of blockers.
     *
     * @param ComActorsDomainEntityActor $actor
     */
    public function addBlocker($actor)
    {
        //if A blocks B, then A must remove B as a follower
        //need to keep track of this since the mixin is a singleton
        $person = $this->_mixer;

        if ($person->eql($actor)) {
            return false;
        }

        $person->removeFollower($actor);

        $actor->removeFollower($person);

        //just in case
        $person->removeRequester($actor);

        $actor->removeRequester($person);

        $edge = $this->getService('repos:actors.block')->findOrAddNew(array(
                'blocker' => $actor,
                'blocked' => $person,
            ));

        $edge->save();

        $this->resetStats(array($actor, $person));

        return $edge;
    }

    /**
     * Removes a person from the list of blocked.
     *
     * @param ComActorsDomainEntityActor $person
     */
    public function removeBlocker($actor)
    {
        $person = $this->_mixer;

        $data = array(
            'blocker' => $actor,
            'blocked' => $person,
        );

        $this->getService('repos:actors.block')->destroy($data);

        $this->resetStats(array($actor, $person));
    }

    /**
     * Return this person common leader with another person.
     *
     * @param ComActorsDomainEntityActor $actor Actor for which to get the common leaders
     *
     * @return AnDomainEntitysetDefault
     */
    public function getCommonLeaders($actor)
    {
        if (!isset($this->__common_leaders)) {
            $ids = array_intersect($this->leaderIds->toArray(), $actor->leaderIds->toArray());
            $ids[] = -1;
            $query = $this->getService('repos:actors.actor')->getQuery()->where('id', 'IN', $ids);

            $this->__common_leaders = $query->toEntitySet();
        }

        return $this->__common_leaders;
    }

    /**
     * Return the mutual followers.
     *
     * @return AnDomainEntitysetAbstract
     */
    public function getMutuals()
    {
        if (!isset($this->__mutuals)) {
            $ids = array_intersect($this->leaderIds->toArray(), $this->followerIds->toArray());
            $query = $this->getService('repos:people.person')->getQuery()->where('id', 'IN', $ids);

            $this->__mutuals = $query->toEntitySet();
        }

        return $this->__mutuals;
    }

    /**
     * Return true if the both the mixer and person is following each other
     * else it returns false;.
     *
     * @param ComPeopleDomainEntityPerson $person Person object
     *
     * @return bool
     */
    public function mutuallyLeading($person)
    {
        return $this->leading($person) && $this->following($person);
    }

    /**
     * Return true if the mixer is following the person else return false.
     *
     * @param ComActorsDomainEntityActor $actor Actor object
     *
     * @return bool
     */
    public function following($actor)
    {
        return $this->_mixer->leaderIds->offsetExists($actor->id);
    }
}
