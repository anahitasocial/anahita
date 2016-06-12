<?php

/**
 * Actor Settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerActor extends ComBaseControllerResource
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'toolbars' => array($this->getIdentifier()->name, 'menubar'),
        ));
    }

    protected function _actionGet(KCommandContext $context)
    {
        $title = JText::_('COM-SETTINGS-HEADER-ACTORS');

        $this->getToolbar('menubar')->setTitle($title);

        return parent::_actionGet($context);
    }

    protected function _actionEdit(KCommandContext $context)
    {
      $data = $context->data;

      if ($app = $this->getService('repos:components.component')->fetch(array('id' => $data->app))) {
          $app->setAssignmentForIdentifier($data->actor, $data->access);
          $app->save();
      }
    }
}
