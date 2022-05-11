<?php

/**
 * Plugins Settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.Anahita.io
 */

class ComSettingsControllerPlugin extends ComBaseControllerService
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
                'limit' => 99,
                'type' => '',
            ),
            'behaviors' => array(
                'enablable'
            )
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
        
        $filter = $this->getService('anahita:filter.alpha');

        if ($type = $filter->sanitize($this->type)) {
            $entities->where('folder', '=', $type);
        }
        
        $entities->order($filter->sanitize($this->sort));

        return $entities;
    }
}
