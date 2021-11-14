<?php

/**
 * Permissionable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsControllerBehaviorPermissionable extends AnControllerBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback(
            array('after.setpermission'), 
            array($this, 'fetchPermission')
        );
    }
        
    /**
     * Get permissions for a permissionable entity.
     *
     * @param AnCommandContext $context Context parameter
     */    
    protected function _actionGetpermissions(AnCommandContext $context)
    {
        $data = array();
        $actor = $this->getItem();
        $components = $this->getItem()->components;
        
        foreach ($components as $component) {
            $actions = array();
            
            if (! $component->isAssignable()) {
                continue;
            }

            if (! count($component->getPermissions())) {
                continue;
            }
            
            $actions = $this->_getActions($component);  
            
            $data[] = array(
                'id' => $component->id,
                'name' => $component->component,
                'description' => $component->getProfileDescription(), 
                'enabled' => true, 
                'actions' => $actions,
           );    
        }

        $this->getView()
        ->set('data', $data)
        ->set('pagination', array(
            'offset' => 0,
            'limit' => 20,
            'total' => count($data),
        ));
        
        return $data;
    }
    
    /**
     * Set a permission for a permissionable entity.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionSetpermission(AnCommandContext $context)
    {
        $data = $context->data;

        $names = AnConfig::unbox($data->privacy_name);

        settype($names, 'array');
        
        foreach ($names as $name) {
            $this->getItem()->setPermission($name, $data->$name);
        }
    }
    
    /**
     * Set a permission for a permissionable entity.
     *
     * @param AnCommandContext $context Context parameter
     */
    public function fetchPermission(AnCommandContext $context)
    {
        $data = array();
        $actions = array();
        $actor = $this->getItem();
        $privacyNames = AnConfig::unbox($context->data->privacy_name);

        $package = explode(':', $privacyNames[0])[0];
        $component = $this->getItem()->components->find(array('component' => $package));
        $actions = $this->_getActions($component); 
        
        $content = json_encode(array(
            'id' => $component->id,
            'name' => $component->component,
            'description' => $component->getProfileDescription(), 
            'enabled' => true, 
            'actions' => $actions,
       ));
       
       $context->response->setContent($content); 
    }
    
    /**
    * Get actions of a component
    * 
    * @param ComComponentsDomainEntityComponent
    * @return array of array( $action => $value )
    */
    protected function _getActions($component)
    {
        $data = array();
        
        foreach ($component->getPermissions() as $entityIdentifier => $actions) {
            $identifier = $this->getIdentifier($entityIdentifier);
            
            foreach ($actions as $action) {
                $key = 'com_' . $identifier->package.':'.$identifier->name.':'.$action;
                $value = $this->getItem()->getPermission($key, LibBaseDomainBehaviorPrivatable::FOLLOWER);
                $data[$key] = $value;
            }
        }  
        
        return $data;
    }
    
    /**
     * Authorize getting permissions.
     *
     * @return bool
     */
    public function canGetpermissions()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
    
    /**
     * Authorize setting permissions.
     *
     * @return bool
     */
    public function canSetpermission()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}