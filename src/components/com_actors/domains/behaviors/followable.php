<?php

/**
 * Followable Behavior.
 *
 * This behavior provides methods for an actor object to add/remove follower and at
 * same time block/unblock unwanted connetions.
 *
 * <code>
 * //fetches a peron with $id
 * $actor  = AnService::get('repos:actors.actor')->fetch($some_actor_id);
 * $person = AnService::get('repos:people.person')->fetch($some_person_id);
 * if ( $actor->isFollowable() )
 * {
 *      //adding the person as a follower
 *      $actor->addFollower($person);
 *      //if the person is following the actor, then actor is leading
 *      //the person
 *      if ( $actor->leading($person) )
 *          print 'actor is leading the person or person is following the actor'
 *
 *      $actor->followers //return the actors followers
 * }
 * </code>
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsDomainBehaviorFollowable extends AnDomainBehaviorAbstract
{
    /**
     * A flag to whether subscribe to an actor after following
     * or not.
     *
     * @var bool
     */
    protected $_subscribe_after_follow;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_subscribe_after_follow = $config->subscribe_after_follow;
    }

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

            'subscribe_after_follow' => $config->mixer->isSubscribable(),

            'attributes' => array(
                'allowFollowRequest' => array('default' => false),
                'followRequesterIds' => array(
                        'type' => 'set', 
                        'default' => 'set', 
                        'write' => 'private',
                    ),
                'followerCount' => array(
                        'default' => 0, 
                        'write' => 'private',
                    ),
                'followerIds' => array(
                        'type' => 'set', 
                        'default' => 'set', 
                        'write' => 'private',
                    ),
                'blockedIds' => array(
                        'type' => 'set', 
                        'default' => 'set', 
                        'write' => 'private',
                    ),
                'blockerIds' => array(
                        'type' => 'set', 
                        'default' => 'set', 
                        'write' => 'private',
                    ),
            ),

            'relationships' => array(
                'requesters' => array(
                    'parent_delete' => 'ignore',
                    'through' => 'com:actors.domain.entity.request',
                    'target' => 'com:actors.domain.entity.actor',
                    'child_key' => 'requestee',
                ),
                'followers' => array(
                    'parent_delete' => 'ignore',
                    'through' => 'com:actors.domain.entity.follow',
                    'target' => 'com:actors.domain.entity.actor',
                    'child_key' => 'leader',
                ),
                'blockeds' => array(
                    'parent_delete' => 'ignore',
                    'through' => 'com:actors.domain.entity.block',
                    'target' => 'com:actors.domain.entity.actor',
                    'child_key' => 'blocker',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Add a follow requester to the actor.
     *
     * @param ComActorsDomainEntityActor $requester
     */
    public function addRequester($requester)
    {
        $leader = $this->_mixer;

        if ($leader->eql($requester)) {
            return false;
        }

        if ($requester->following($leader)) {
            return false;
        }

        if ($leader->blocking($requester)) {
            return false;
        }

        $edge = $this->getService('repos:actors.request')
                        ->findOrAddNew(array(
                            'requester' => $requester,
                            'requestee' => $leader,
                        ));

        $edge->save();

        $this->resetStats(array($leader, $requester));
    }

    /**
     * Removes an actor from the list of blocked.
     *
     * @param ComActorsDomainEntityActor $actor The actor to block
     */
    public function removeRequester($requester)
    {
        $leader = $this->_mixer;
        $data = array(
            'requester' => $requester,
            'requestee' => $leader,
        );

        $this->getService('repos:actors.request')->destroy($data);
        $this->resetStats(array($leader, $requester));
    }

    /**
     * Add a follower to the actor.
     *
     * @param ComActorsDomainEntityActor $actor The actor to block
     */
    public function addFollower($follower)
    {
        $leader = $this->_mixer;

        if ($leader->eql($follower)) {
            return false;
        }

        if ($leader->blocking($follower)) {
            return false;
        }

        if ($follower->requested($leader)) {
            $leader->removeRequester($follower);
        }

        $edge = $this->getService('repos:actors.follow')
                        ->findOrAddNew(array(
                            'leader' => $leader,
                            'follower' => $follower,
                        ));

        $edge->save();

        //add a subscriber
        //if ($this->_subscribe_after_follow) {
            //$leader->addSubscriber($follower);
        //}

        $this->resetStats(array($leader, $follower));

        return $edge;
    }

    /**
     * Remove an actor to from list of the followers. If the
     * leader is subscribable then it will remove the follower from the list
     * of it's subscribers.
     *
     * @param ComActorsDomainEntityActor $follower
     */
    public function removeFollower($follower)
    {
        $leader = $this->_mixer;

        if ($leader->isSubscribable()) {
            $leader->removeSubscriber($follower);
        }

        $this->removeNodeSubscriptions($leader, $follower);

        if ($leader->isAdministrable() && $leader->hasAdmin($follower)) {
            $result = $leader->administrators->extract($follower);
            
            if (! $result) {
                $leader->resetStats(array($leader, $follower));
            }
        }

        $this->getService('repos:actors.follow')
                ->destroy(array(
                    'follower' => $follower,
                    'leader' => $leader,
                ));

        $this->resetStats(array($leader, $follower));
    }

    /**
     * Add a leader to the actor.
     *
     * @param ComActorsDomainEntityActor $actor The actor to block
     */
    public function addLeader($leader)
    {
        $follower = $this->_mixer;

        if ($follower->eql($leader)) {
            return false;
        }

        if ($leader->blocking($follower)) {
            return false;
        }

        if ($follower->requested($leader)) {
            $leader->removeRequester($follower);
        }

        $edge = $this->getService('repos:actors.follow')
                        ->findOrAddNew(array(
                            'leader' => $leader,
                            'follower' => $follower,
                        ));

        $edge->save();

        //add a subscriber
        if ($this->_subscribe_after_follow) {
            $leader->addSubscriber($follower);
        }

        $this->resetStats(array($leader, $follower));

        return $edge;
    }

    /**
     * Removes a leader from the list of the leaders. If the
     * leader is subscribable then it will remove the follower from the list
     * of it's subscribers.
     *
     * @param ComActorsDomainEntityActor $follower
     */
    public function removeLeader($leader)
    {
        $follower = $this->_mixer;

        if ($leader->isSubscribable()) {
            $leader->removeSubscriber($follower);
        }

        $this->removeNodeSubscriptions($leader, $follower);

        if ($leader->isAdministrable() && $leader->hasAdmin($follower)) {
            $result = $leader->administrators->extract($follower);
            
            if (! $result) {
                $leader->resetStats(array($leader, $follower));
            }
        }

        $this->getService('repos:actors.follow')
                ->destroy(array(
                    'follower' => $follower,
                    'leader' => $leader,
                ));

        $this->resetStats(array($leader, $follower));
    }

    /**
     * Adds an $person to a list of blocked actors.
     *
     * @param ComActorsDomainEntityActor $person
     */
    public function addBlocked($person)
    {
        //if A blocks B, then A must remove B as a follower
        //need to keep track of this since the mixin is a singleton
        $leader = $this->_mixer;

        if ($leader->eql($person)) {
            return false;
        }

        $person->removeFollower($leader);
        $leader->removeFollower($person);

        //just in case
        $person->removeRequester($leader);
        $leader->removeRequester($person);

        $edge = $this->getService('repos:actors.block')
                        ->findOrAddNew(array(
                            'blocker' => $leader,
                            'blocked' => $person,
                        ));

        $edge->save();
        $this->resetStats(array($leader, $person));

        return $edge;
    }

    /**
     * Removes a person from the list of blocked.
     *
     * @param ComActorsDomainEntityActor $person
     */
    public function removeBlocked($person)
    {
        $leader = $this->_mixer;

        $data = array(
            'blocker' => $leader,
            'blocked' => $person,
        );

        $this->getService('repos:actors.block')->destroy($data);
        $this->resetStats(array($leader, $person));
    }

    /**
     * Return true if mixer is leading the actor else return false.
     *
     * @param ComActorsDomainEntityActor $actor The actor to block
     *
     * @return bool
     */
    public function leading($actor)
    {
        return $this->_mixer->followerIds->offsetExists($actor->id);
    }

    /**
     * Return true if the actor has been requested to be followed.
     *
     * @param ComActorsDomainEntityActor $requester The requested
     *
     * @return bool
     */
    public function requested($leader)
    {
        return $leader->followRequesterIds->offsetExists($this->_mixer->id);
    }

    /**
     * Return true if the mixer is blocking another actor else return false.
     *
     * @param ComActorsDomainEntityActor $actor The actor to block
     *
     * @return bool
     */
    public function blocking($actor)
    {
        return $this->_mixer->blockedIds->offsetExists($actor->id);
    }

    /**
     * Adds a filter to the query based on the access mode.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeRepositoryFetch(AnCommandContext $context)
    {
        if (AnService::has('viewer')) {
            $query = $context->query;
            $repository = $query->getRepository();
            $viewer = get_viewer();

            if ($viewer->id) {
                $query->where("IF( FIND_IN_SET({$viewer->id}, @col(blockedIds)), 0, 1)");
            }
        }
    }

    /**
     * Remove all the subscriptions of a follower to the nodes owned by a leader.
     *
     * @param ComActorsDomainEntityActor $leader
     * @param ComActorsDomainEntityActor $follower
     */
    public function removeNodeSubscriptions($leader, $follower)
    {
        //Find all the nodes that follower was subscribed to
        $query = $this->getService('repos:base.subscription')
                 ->getQuery()->subscriber($follower)
                 ->where('subscribee.id', 'IN', $this->getService('repos:base.node')
                 ->getQuery()->columns('id')->where('owner_id', '=', $leader->id));

        $subscribers = $query->disableChain()->fetchValues('subscribee.id');

        //Remove the follower as subscriber from any node that's owned by the leader
        if (count($subscribers)) {
            $this->getService('repos:base.node')
            ->update("subscriber_ids = @remove_from_set(subscriber_ids,{$follower->id}), subscriber_count = @set_length(subscriber_ids)", $subscribers);

            $this->getService('repos:base.subscription')->destroy($query);
        }
    }
}
