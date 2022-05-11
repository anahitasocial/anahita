<?php

/**
 * Edge represent a connection between two nodes. An edge can be used any context but it
 * must be created through a social application.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseDomainEntityEdge extends AnDomainEntityDefault
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
            'inheritance' => array(
                'abstract' => $this->getIdentifier()->classname === __CLASS__,
                'column' => 'type',
                'ignore' => array(),
            ),
            'resources' => array(
                array(
                    'name' => 'edges',
                    'alias' => $this->getIdentifier()->name,
                ),
            ),
            'attributes' => array(
                'id' => array('key' => true),
            ),
            'behaviors' => array(
                'modifiable',
            ),
            'relationships' => array(
                'nodeA' => array(
                        'required' => true,
                        'polymorphic' => true,
                        'parent' => 'com:base.domain.entity.node',
                    ),
                'nodeB' => array(
                    'required' => true,
                    'polymorphic' => true,
                    'parent' => 'com:base.domain.entity.node',
                    ),
            ),
        ));

        return parent::_initialize($config);
    }

/**
 * Validates an entity.
 *
 * @param AnCommandContext $context
 */
    //protected function _beforeEntityInsert(AnCommandContext $context)
    protected function _onEntityValidate(AnCommandContext $context)
    {
        if ($this->nodeA->id === $this->nodeB->id) {
            return false;
        }

        return true;
    }
}
