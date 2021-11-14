<?php

/**
 * An edge that subscribes a subsriber (nodeA) to a subscribee (nodeB). The subscription edge
 * must always be between a {@link ComPeopleDomainEntityPerson person} and any other node.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseDomainEntitySubscription extends ComBaseDomainEntityEdge
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
            'aliases' => array(
                'subscriber' => 'nodeA',
                'subscribee' => 'nodeB',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Resets the votable stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        $this->subscribee->getRepository()
                         ->getBehavior('subscribable')
                         ->resetStats(array($this->subscribee));
    }

    /**
     * Resets the votable stats.
     *
     * AnCommandContext $context Context
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->subscribee->getRepository()
                         ->getBehavior('subscribable')
                         ->resetStats(array($this->subscribee));
    }
}
