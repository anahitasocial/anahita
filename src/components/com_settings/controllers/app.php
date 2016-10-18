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
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
             'request' => array(
                'sort' => 'name',
                'limit' => 99
             ),
        ));

        parent::_initialize($config);
    }

    protected function _actionGet(KCommandContext $context)
    {
        $title = AnTranslator::_('COM-SETTINGS-HEADER-APPS');

        $this->getToolbar('menubar')->setTitle($title);

        return parent::_actionGet($context);
    }

    protected function _actionEdit(KCommandContext $context)
    {
        parent::_actionEdit($context);

        if (!$context->getError()) {
            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
        }
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

        $entities->order($this->sort);

        return $entities;
    }
}
