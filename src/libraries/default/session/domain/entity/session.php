<?php

/**
 * Session Entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibSessionDomainEntitySession extends AnDomainEntityDefault
{
    const MAX_LIFETIME = 60 * 24 * 3600;

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
            'resources' => array('session'),
            'searchable_properties' => array(
                'userId',
                'username',
                'usertype'
            ),
            'attributes' => array(
                'sessionId' => array(
                    'key' => true,
                    'default' => '',
                    'required' => true
                ),
                'username' => array(
                    'default' => ''
                ),
                'userid' => array(
                    'default' => 0
                ),
                'usertype' => array(
                    'default' => 'guest'
                ),
                'guest' => array(
                    'default' => 1
                ),
                'data' => array(
                    'default' => ''
                ),
                'time' => array(
                    'default' => 0
                )
            ),
            'aliases' => array(
                'id' => 'sessionId',
                'meta' => 'data'
            )
        ));

        parent::_initialize($config);
    }
}
