<?php

/**
 * Templates Settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerTemplate extends ComBaseControllerResource
{
    /**
    *  @param entity object
    */
    protected $_entity;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('before.read', array($this, 'fetchEntity'));
        $this->registerCallback('before.edit', array($this, 'fetchEntity'));
    }

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

    /**
    *   read service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return boolean
    */
    protected function _actionGet(KCommandContext $context)
    {
        $title = AnTranslator::_('COM-SETTINGS-HEADER-TEMPLATES');

        $this->getToolbar('menubar')->setTitle($title);

        return parent::_actionGet($context);
    }

    /**
    *   browse service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return array of entities
    */
    protected function _actionBrowse(KCommandContext $context)
    {
        $exclude = array(
          'base',
          'system',
          '.',
          '..',
          '.DS_Store'
        );

        $aliases = array_diff(scandir(ANPATH_THEMES), $exclude);
        $items = array();

        foreach ($aliases as $alias) {
            if ($item = $this->getService('com:settings.domain.entity.template')->load($alias)) {
               $items[] = $item;
            }
        }

        $this->getView()->set('items', $items);
    }

    /**
    *   read service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionRead(KCommandContext $context)
    {
        $this->getView()->set('item', $this->_entity);
    }

    /**
    *   edit service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(KCommandContext $context)
    {
        $data = $context->data;

        //don't overwrite these two
        unset($data->secret);
        unset($data->dbtype);

        $this->_entity->setData(KConfig::unbox($data));

        if ($this->_entity->save()) {
            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
        }
    }

    /**
    * method to fetch setting entity
    *
    *  @param KCommandContext $context Context Parameter
    *
    *  @return ComSettingsDomainEntitySetting object
    */
    public function fetchEntity(KCommandContext $context)
    {
        if (!$this->_entity) {
            if ($entity = $this->getService('com:settings.domain.entity.template')->load($this->alias)) {
                $this->_entity = $entity;
            }
        }

        return $this->_entity;
    }
}
