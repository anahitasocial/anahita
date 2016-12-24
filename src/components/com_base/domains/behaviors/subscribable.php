<?php

/**
 * Subscribable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDomainBehaviorSubscribable extends AnDomainBehaviorAbstract
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
                'subscriberCount' => array(
                    'default' => 0,
                    'write' => 'private', ),
                'subscriberIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private', ),
            ),
            'relationships' => array(
                'subscriptions' => array(
                    'child' => 'com:base.domain.entity.subscription',
                    'child_key' => 'subscribee',
                    'parent_delete' => 'ignore', ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Subscribe the author to the node.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        if ($this->isOwnable() && $this->owner->isNotifiable() && !$this->owner->eql($this->author)) {
            $this->addSubscriber($this->owner);
        } elseif (!empty($this->author)) {
            $this->addSubscriber($this->author);
        }
    }

    /**
     * Return whether the person's is subsribed or not.
     *
     * @param ComPeopleDomainEntityPerson $person Subscriber
     *
     * @return bool
     */
    public function subscribed($subscriber)
    {
        if ($subscriber->id && $this->subscriberIds->offsetExists($subscriber->id)) {
            return true;
        }

        return false;
    }

    /**
     * Adds a person as the subscriber.
     *
     * @param ComPeopleDomainEntityPerson $person Subscriber
     */
    public function addSubscriber($person)
    {
        if ($this->eql($person)) {
            return false;
        }

        if($this->subscribed($person)){
           return false;
        }

        $subscription = $this->subscriptions->findOrAddNew(array(
            'subscriber' => $person,
        ))->setData(array(
            'component' => $this->component,
            'author' => $person,
        ), AnDomain::ACCESS_PROTECTED);

        return $this->_mixer;
    }

    /**
     * Removes a person from the list of subscriber of the node.
     *
     * @param ComPeopleDomainEntityPerson $person Subscriber
     */
    public function removeSubscriber($person)
    {
        $mixer = $this->_mixer;
        $subscription = $this->subscriptions
                             ->find(array(
                                'subscriber' => $person,
                                'subscribee' => $mixer, ));

        if ($subscription) {
            $this->subscriptions->extract($subscription);
            return $this->_mixer;
        }
    }

    /**
     * Reset subscriptions stats.
     *
     * @param array $entities
     */
     public function resetStats(array $entities)
     {
         foreach ($entities as $entity) {
            $ids = $entity->subscriptions
                          ->getQuery()
                          ->disableChain()
                          ->fetchValues('subscriber.id');

            $entity->set('subscriberCount', count($ids));
            $entity->set('subscriberIds', AnDomainAttribute::getInstance('set')->setData($ids));
            $entity->save();
        }
    }
}
