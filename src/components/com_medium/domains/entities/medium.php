<?php

/**
 * Medium Nodes. Represents medium entities. most of the assets nodes in a social network is
 * a subclass of a medium node.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMediumDomainEntityMedium extends ComBaseDomainEntityNode
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
            'inheritance' => array('abstract' => $this->getIdentifier()->classname === __CLASS__),
            'attributes' => array(
                'enabled' => array(
                    'default' => 1,
                ),
            ),
            'relationships' => array(
                  'author' => array(
                      'parent' => 'com:people.domain.entity.person',
                      'child_column' => 'created_by',
                      'required' => true,
                   ),
            ),
            'behaviors' => array(
                'votable',
                'authorizer',
                'privatable',
                'ownable',
                'dictionariable',
                'subscribable',
                'describable',
                'com:hashtags.domain.behavior.hashtaggable',
                'com:people.domain.behavior.mentionable',
                'com:locations.domain.behavior.geolocatable',
            ),
        ));

        $behaviors = $config->behaviors;

        $behaviors->append(array(
            'modifiable' => array(
                'modifiable_properties' => array('name', 'body'),
            ),
            'commentable' => array('comment' => array('length' => 5000)),
        ));

        parent::_initialize($config);
    }
}
