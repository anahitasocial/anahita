<?php

/**
 * * Basic Anahita Node. A node within the social network represents an object with a distinguished identity.
 * A person, a photo, a group are good example of a node. Subclasses adds more bevahior to a basic node by extending the node.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDomainEntityNode extends AnDomainEntityDefault
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
            'inheritance' => array(
                'abstract' => $this->getIdentifier()->classname === __CLASS__,
                'column' => 'type',
                'ignore' => array(),
            ),
            'resources' => array(
                array(
                    'alias' => $this->getIdentifier()->name,
                    'name' => 'nodes'
                 )
             ),
            'identity_property' => 'id',
            'attributes' => array(
                'id' => array(
                    'key' => true,
                    'type' => 'integer',
                    'read' => 'public',
                ),
                'component' => array(
                    'required' => true,
                    'read' => 'public',
                ),
                'enabled' => array(
                    'default' => 1
                ),
                'verified' => array(
                    'default' => 0
                )
            ),
           'behaviors' => to_hash('node'),
        ));

        parent::_initialize($config);
    }

    /**
     * Initialize a new node.
     */
    protected function _afterEntityInstantiate(KConfig $config)
    {
        $config->append(array('data' => array(
            'component' => 'com_'.$this->getIdentifier()->package,
        )));
    }
}
