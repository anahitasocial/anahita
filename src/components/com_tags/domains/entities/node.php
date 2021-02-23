<?php

/**
 * Tag entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTagsDomainEntityNode extends ComBaseDomainEntityNode
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
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'string',
                    'read' => 'public',
                    'unique' => true,
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'enabled' => array('default' => 1)  
            ),
            'inheritance' => array(
                'abstract' => $this->getIdentifier()->classname === __CLASS__
            ),
            'relationships' => array(
                'taggables' => array(
                    'through' => 'com:' . $this->getIdentifier()->package . '.domain.entity.tag',
                    'child_key' => $this->getIdentifier()->name,
                    'target' => 'com:base.domain.entity.node',
                    'target_child_key' => 'taggable',
                ),
            ),
        ));
        
        parent::_initialize($config);
    }
}
