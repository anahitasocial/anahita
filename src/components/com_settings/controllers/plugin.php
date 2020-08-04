<?php

/**
 * Plugins Settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
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
    *   get service
    *
    *  @param AnCommandContext $context Context Parameter
    *  @return object entity
    */
    protected function _actionGet(AnCommandContext $context)
    {
        $title = AnTranslator::_('COM-SETTINGS-HEADER-PLUGINS');

        $this->getToolbar('menubar')->setTitle($title);

        return parent::_actionGet($context);
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

    /**
    *   edit service
    *
    *  @param AnCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(AnCommandContext $context)
    {
        $entity = parent::_actionEdit($context);

        if (!$context->getError()) {
            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
        }

        return $entity;
    }
}
