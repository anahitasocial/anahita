<?php

/**
 * Shared Ownership Edge.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDomainEntityOwnership extends ComBaseDomainEntityEdge
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
     * KCommandContext $context Context
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        $this->ownable->getRepository()->getBehavior('sharable')->resetStats(array($this->ownable));
    }

    /**
     * Resets the votable stats.
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {
        $this->ownable->getRepository()->getBehavior('sharable')->resetStats(array($this->ownable));
    }
}
