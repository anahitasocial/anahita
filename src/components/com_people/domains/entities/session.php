<?php

/**
 * Session Entity
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainEntitySession extends AnDomainEntityDefault
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
            'resources' => array('session'),
            'attributes' => array(
                'session_id' => array('key' => true)
            ),
            'relationships' => array(
                'parent' => 'com:people.domain.entity.person',
                'child_column' => 'person_userid',
                'parent_column' => 'userid',
                'required' => true,
            ),
        ));

        parent::_initialize($config);
    }
}
