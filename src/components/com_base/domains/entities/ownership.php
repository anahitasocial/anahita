<?php

/**
 * Shared Ownership Edge.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseDomainEntityOwnership extends ComBaseDomainEntityEdge
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
            'relationships' => array(
                'owner' => array('parent' => 'com:actors.domain.entity.actor'),
                'ownable' => array('parent' => 'com:base.domain.entity.node'),
            ),
            'aliases' => array(
                'owner' => 'nodeA',
                'ownable' => 'nodeB',
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
        $this->ownable->getRepository()->getBehavior('sharable')->resetStats(array($this->ownable));
    }

    /**
     * Resets the votable stats.
     */
    protected function _afterEntityDelete(AnCommandContext $context)
    {
        $this->ownable->getRepository()->getBehavior('sharable')->resetStats(array($this->ownable));
    }
}
