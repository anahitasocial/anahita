<?php

class ComSubscriptionsControllerPackage extends ComBaseControllerService
{
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback(array('before.edit', 'after.add'), array($this, 'setMeta'));

    }
        
    /**
     * Read a package
     * 
     * @param KCommandContext $context
     * @return void
     */ 
    public function _actionRead($context)
    {
        $this->plugins = JPluginHelper::getPlugin('subscriptions'); 
               
        return parent::_actionRead($context);
    }   
    
    /**
     * Set the entity gid
     * 
     * @param KCommandContext $context
     * @return boolean
     */
    public function setMeta(KCommandContext $context)
    {
        $data        = $context->data;
        $entity      = $this->getItem();
        $plugins     = KConfig::unbox(pick($data->plugins, array()));
        $entity->setPluginsValues($plugins);
    }
}