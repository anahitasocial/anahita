<?php

/**
 * System settings Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class ComSettingsControllerConfig extends ComBaseControllerResource
{

    /**
    *  @param entity object
    */
    protected $_entity;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('before.get', array($this, 'fetchEntity'));
        $this->registerCallback('before.edit', array($this, 'fetchEntity'));
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'toolbars' => array($this->getIdentifier()->name, 'menubar'),
        ));
    }

    /**
    *   read service
    *
    *  @param AnCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionGet(AnCommandContext $context)
    {
        $title = AnTranslator::_('COM-SETTINGS-HEADER-CONFIGS');

        $this->getToolbar('menubar')->setTitle($title);
        $this->getView()->set('config', $this->_entity);
        
        parent::_actionGet($context);
    }

    /**
    *   edit service
    *
    *  @param AnCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionEdit(AnCommandContext $context)
    {
        $data = AnConfig::unbox($context->data);
        $data = isset($data['meta']) ? $data['meta'] : $data;

        //don't overwrite these two
        unset($data['secret']);
        unset($data['dbtype']);

        $this->_entity->setData($data);

        if ($this->_entity->save()) {
            $this->setMessage('COM-SETTINGS-PROMPT-SUCCESS', 'success');
            
            $content = $this->getView()
            ->set('config', $this->_entity)
            ->display();
            
            $context->response->setContent($content);
        }
        
        return;
    }

    /**
    * method to fetch setting entity
    *
    *  @param AnCommandContext $context Context Parameter
    *
    *  @return ComSettingsDomainEntityConfig object
    */
    public function fetchEntity(AnCommandContext $context)
    {
        if (!$this->_entity) {
            $this->_entity = $this->getService('com:settings.domain.entity.config')->load();
        }

        return $this->_entity;
    }
}
