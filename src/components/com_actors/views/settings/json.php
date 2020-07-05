<?php

/**
 * The actor setting JSON representation.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2020 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsViewSettingsJson extends ComBaseViewJson
{
    public function display()
    {
        $data = array();
        
        $data['actor'] =  $this->_state->getItem()->toSerializableArray();
        
        if ($followRequests = $this->_state->getItem()->requesters->toArray()) {
            $data['followRequests'] = $followRequests;
        }
        
        $data['permissions'] = $this->_getPermissions();
        
        if ($apps = $this->_getOptionalApps()) {
            $data['apps'] = $apps;
        }
        
        if ($this->_state->getItem()->isAdministrable()) {
            $data['administrators'] = $this->_state->getItem()->administrators->toSerializableArray(); 
        }
             
        $data = array_merge($data, $this->_addExtended());

        return json_encode($data);
    }
    
    /**
    *   Gets settings for actor app permissions
    *
    *   @return array 
    */
    protected function _getPermissions() 
    {
        $config = new AnConfig();
        $components = $this->_state->getItem()->components;
        
        foreach ($components as $component) {
            $permissions = array();

            if (! $component->isAssignable()) {
                continue;
            }

            if (! count($component->getPermissions())) {
                continue;
            }

            foreach ($component->getPermissions() as $identifier => $actions) {
                if (strpos($identifier, '.') === false) {
                    $name = $identifier;
                    $identifier = clone $component->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;
                }
                
                $identifier = $this->getIdentifier($identifier);
                
                foreach ($actions as $action) {
                    $key = $identifier->package.':'.$identifier->name.':'.$action;
                    $value = $this->_state->getItem()->getPermission($key);
                    $permissions[] = array('name' => $key, 'value' => $value);
                }
                
                $config->append(array(
                    $component->component => array(
                       'name' => $component->component, 
                       'enabled' => true, 
                       'permissions' => $permissions
                    ),
                ));
            }
        }
        
        return $config->toArray();
    }
    
    protected function _getOptionalApps() 
    {
        $data = array();
        $apps = $this->getService('com:actors.domain.entityset.component', array(
                'actor' => $this->_state->getItem(),
                'can_enable' => true,
            ));
            
        foreach ($apps as $app) {
            $data[] = array(
                'name' => $app->getProfileName(),
                'description' => $app->getProfileDescription(),
                'enabled' => $app->enabledForActor($this->_state->getItem()),
            );
        }    
            
        return $data;
    }
    
    /**
    *   Adds extended settings from other components and apps that
    *   are assigned to this actor
    *
    *   @return array 
    */
    protected function _addExtended() 
    {
        $data = array();
        
        $tabs = new LibBaseTemplateObjectContainer();
        
        $this->getService('anahita:event.dispatcher')
        ->dispatchEvent('onSettingDisplay', array(
            'actor' => $this->_state->getItem(),
            'tabs' => $tabs,
        ));

        foreach ($tabs as $tab) {
            if ($tab->controller) {
                $content = $this->getService($tab->controller)
                ->oid($this->_state->getItem()->id)
                ->format('json')
                ->display();
                $data[$tab->name] = json_decode($content);
            }
        }
        
        return $data;
    }
}
