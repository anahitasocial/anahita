<?php

/**
 * Assignement Node.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainEntityAssignment extends ComBaseDomainEntityNode
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
                'actortype' => array('column' => 'name'),
                'access',
             ),
            'relationships' => array(
                'componentEntity' => array(
                    'type' => 'belongs_to',
                    'child_column' => 'component',
                    'parent_key' => 'component',
                    'parent' => 'com:components.domain.entity.assignment'
                ),
                'actor' => array(
                    'type' => 'belongs_to',
                    'child_column' => 'owner_id',
                    'type_column' => 'owner_type',
                    'polymorphic' => true
                ),
             ),
        ));

        parent::_initialize($config);
    }
}
