<?php

/**
 * Plugin Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityPlugin extends AnDomainEntityDefault
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
            'resources' => array('plugins'),
            'attributes' => array(
                'published' => array('default' => false),
            ),
            'behaviors' => array(
                'orderable',
                'authorizer',
                'locatable',
            ),
            'auto_generate' => true,
        ));

        return parent::_initialize($config);
    }
}
