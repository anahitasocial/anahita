<?php

/**
 * Privatable Behavior.
 *
 * Provides privacy for nodes
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleDomainBehaviorPrivatable extends ComActorsDomainBehaviorPrivatable
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
                'access' => array(
                    'default' => get_config_value('people.access', self::GUEST)
                ),
                'permissions' => array(
                    'type' => 'json',
                    'default' => 'json',
                    'write' => 'private'
                ),
            ),
        ));

        parent::_initialize($config);
    }
}
