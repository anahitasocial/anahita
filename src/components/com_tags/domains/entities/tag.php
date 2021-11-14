<?php

/**
 * Tag association
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComTagsDomainEntityTag extends ComBaseDomainEntityEdge
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
                'abstract' => $this->getIdentifier()->classname === __CLASS__
            ),
            'aliases' => array(
                'taggable' => 'nodeB',
            ),
            'relationships' => array(
                'taggable' => array(
                    'parent' => 'com:base.domain.entity.node',
                ),
            ),
        ));

        parent::_initialize($config);
    }
}
