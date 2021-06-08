<?php

/**
 * Apps Settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerApp extends ComBaseControllerService
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
             'request' => array(
                'sort' => 'name',
                'limit' => 99
             ),
        ));

        parent::_initialize($config);
    }

    /**
    *   browse service
    *
    *  @param AnCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionBrowse(AnCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);
        
        $sort = $this->getService('anahita:filter.alpha')->sanitize($this->sort);

        $entities->order($sort);

        return $entities;
    }
}
