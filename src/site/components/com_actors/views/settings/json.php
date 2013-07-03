<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * The actor setting JSON representation.
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 * @link       http://www.anahitapolis.com
 */
class ComActorsViewSettingsJson extends ComBaseViewJson
{
    public function display()
    {
        $apps = $this->getService('com://site/actors.domain.entityset.component', array(
                'actor' 		=> $this->_state->getItem(),
                'can_enable'   => true
        ));  
        
        $config = new KConfig();       
        
        foreach($this->_state->getItem()->components as $component)
        {
            $permissions = array();
						
			if ( !$component->isAssignable() ) {
				continue;
			}			
			
			if ( !count($component->getPermissions()) ) continue;
			
			foreach($component->getPermissions() as $identifier => $actions ) 
			{
                if ( strpos($identifier,'.') === false )
                {
                    $name = $identifier;
                    $identifier = clone $component->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;
                }
                $identifier = $this->getIdentifier($identifier);
                foreach($actions as $action) 
                {
                    $key   = $identifier->package.':'.$identifier->name.':'.$action;
                    $value = $this->_state->getItem()->getPermission($key);
                    $permissions[] = array('name'=>$key, 'value'=>$value);
                }
                $config->append(array(
                       $component->component => array('name'=>$component->component,'enabled'=>true,'permissions'=>$permissions)
                ));                
            }
        }
        

        foreach($apps as $app)
        {
            $config->append(array(
                    $app->component => array('name'=>$app->component,'enabled'=>$app->enabledForActor($this->_state->getItem()))
            ));
        }

        $data['followRequests'] = $this->_state->getItem()->requesters->toArray();
        $data['apps']     = array_values($config->toArray());
                
        return json_encode($data);
    }
       
}