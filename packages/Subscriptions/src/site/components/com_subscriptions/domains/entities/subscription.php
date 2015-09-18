<?php

/**
 * Subscription of a person with a package.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainEntitySubscription extends ComBaseDomainEntityEdge
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
            'aliases' => array(
                'person' => 'nodeA',
                'package' => 'nodeB',
            ),
            'relationships' => array(
                'person' => array('parent' => 'com:people.domain.entity.person'),
                'package' => array('parent' => 'com:subscriptions.domain.entity.package'),
            ),
            'behaviors' => array(
                'expirable',
                'dictionariable',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the subscription package.
     *
     * @param ComSubscriptionsDomainEntityPackageDefault $package Package
     */
    public function setNodeB($package)
    {
        $this->set('nodeB', $package);
        $this->set('endDate', clone $this->startDate);
        $this->endDate->addSeconds($package->duration);
    }

    /**
     * Returns the timeleft from a subscription in the number of seconds.
     *
     * @return int
     */
    public function getTimeLeft()
    {
        return $this->endDate->getTimestamp() - AnDomainAttributeDate::getInstance()->getTimestamp();
    }

    /**
     * Return whether a subscriptions is expired or not.
     *
     * @return bool
     */
    public function expired()
    {
        return $this->endDate->compare(AnDomainAttributeDate::getInstance()) < 0;
    }

    /**
     * After adding a subscriptions add the person as a follower to all the package actors.
     *
     * KCommandContext $context Context
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        $actorIds = $this->package->getActorIds();

        if (count($actorIds)) {
            $actors = $this->getService('repos://site/actors.actor')
                           ->getQuery(true)
                           ->where('id', 'IN', $actorIds)
                           ->fetchSet();

            foreach ($actors as $actor) {
                $actor->addFollower($this->person);
            }
        }
    }

    /**
     * After deleting a subscription, unfollow the person from all the package actors.
     *
     * KCommandContext $context Context
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {
        $actorIds = $this->package->getActorIds();

        if (count($actorIds)) {
            $actors = $this->getService('repos://site/actors.actor')
                           ->getQuery(true)
                           ->where('id', 'IN', $actorIds)
                           ->fetchSet();

            foreach ($actors as $actor) {
                $actor->removeFollower($this->person);
            }
        }
    }
}
