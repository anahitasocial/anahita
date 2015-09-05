<?php

/**
 * Request edge represents a follow request between two actors.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsDomainEntityRequest extends ComBaseDomainEntityEdge
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
                'requester' => array('parent' => 'com:actors.domain.entity.actor'),
                'requestee' => array('parent' => 'com:actors.domain.entity.actor'),
            ),
            'aliases' => array(
                'requester' => 'nodeA',
                'requestee' => 'nodeB',
            ),
        ));

        parent::_initialize($config);
    }
}
