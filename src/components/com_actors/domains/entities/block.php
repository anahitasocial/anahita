<?php

/**
 * Social Edge represents an edge between two nodes that will be used to the act of nodeA following
 * activities of nodeB. This edge maybe used to find relevant stories for nodeA from the nodes it is
 * following.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsDomainEntityBlock extends ComBaseDomainEntityEdge
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
                'blocker' => array('parent' => 'com:actors.domain.entity.actor'),
                'blocked' => array('parent' => 'com:actors.domain.entity.actor'),
            ),
            'aliases' => array(
                'blocker' => 'nodeA',
                'blocked' => 'nodeB',
            ),
        ));

        parent::_initialize($config);
    }
}
