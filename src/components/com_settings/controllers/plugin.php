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
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'request' => array(
                'sort' => 'name',
                'limit' => 99,
                'type' => ''
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
    *  @param KCommandContext $context Context Parameter
    *  @return object entity
    */
    protected function _actionGet(KCommandContext $context)
    {
        $title = AnTranslator::_('COM-SETTINGS-HEADER-PLUGINS');

        $this->getToolbar('menubar')->setTitle($title);

        return parent::_actionGet($context);
    }

    /**
    *   browse service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionBrowse(KCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);

        if ($this->type) {
            $entities->where('folder', '=', $this->type);
        }

        $entities->order($this->sort);

        return $entities;
    }

    /**
    *   edit service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(KCommandContext $context)
    {
        $entity = parent::_actionEdit($context);

        if (!$context->getError()) {
            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
        }

        return $entity;
    }
}
