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

class ComSettingsControllerSetting extends ComBaseControllerResource
{
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
    *   browse service
    *
    *  @param KCommandContext $context Context Parameter
    *  @return void
    */
    protected function _actionRead(KCommandContext $context)
    {
        $this->getView()->set('setting', $this->_entity);
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

        foreach($data as $key => $value){
            $this->_entity->$key = $value;
        }

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
            $this->_entity = $this->getService('com://site/settings.domain.entity.setting')->load();
        }

        return $this->_entity;
    }
}
