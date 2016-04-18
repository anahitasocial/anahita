<?php

/**
 * App Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComSettingsDomainEntityApp extends AnDomainEntityDefault
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
            'resources' => array('components'),
            'attributes' => array(
                'params' => array(
                    'required' => false,
                    'default' => ''
                ),
                'enabled' => array('default' => true),
            ),
            'behaviors' => array(
                'orderable',
                'authorizer',
                'locatable',
            ),
            'query_options' => array(
                'where' => array(
                    'parent' => 0,
                    'iscore' => 0
                )
            ),
            'aliases' => array(
                'package' => 'option',
             ),
            'auto_generate' => true,
        ));

        return parent::_initialize($config);
    }

    /**
     * (non-PHPdoc).
     *
     * @see AnDomainEntityAbstract::__get()
     */
    public function __get($key)
    {
        if ($key == 'name') {
            return ucfirst(str_replace('com_', '', $this->option));
        }

        return parent::__get($key);
    }
}
