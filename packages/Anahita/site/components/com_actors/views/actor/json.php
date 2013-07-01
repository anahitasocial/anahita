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
 * The actor JSON representation. For read operation this class provides more 
 * information about an actor
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 * @link       http://www.anahitapolis.com
 */
class ComActorsViewActorJson extends ComBaseViewJson
{
    /**
     * (non-PHPdoc)
     * @see LibBaseViewJson::_getItem()
     */
    protected function _getItem()
    {
        $item = parent::_getItem();
        $context = new KCommandContext();
        $context->gadgets 		= new LibBaseTemplateObjectContainer();
        $context->actor	  		= $this->_state->getItem();
        $context->composers     = new LibBaseTemplateObjectContainer();
        $context->profile       = new KConfig();
        
        if ( $this->_state->getItem()->authorize('access') )
        {
            $this->_state->getItem()->components->registerEventDispatcher( $this->getService('anahita:event.dispatcher'));
             
            $this->getService('anahita:event.dispatcher')->dispatchEvent('onProfileDisplay', $context);
             
            $this->getService('anahita:event.dispatcher')->dispatchEvent('onProfileDisplay', $context);
        
            dispatch_plugin('profile.onDisplay', array('actor'=>$this->_state->getItem(), 'profile'=>$context->profile));
        
            $this->profile = $context->profile;
        }
        
        $item['gadgets']     = array_map(function($gadget) {
            return $gadget->name;
        }, $context['gadgets']->getObjects());
        
        $item['composers']     = array_map(function($gadget) {
                return $gadget->name;
            }, $context['composers']->getObjects());
                            
        $item['information'] = $context['profile'];
        return $item;
    }    
}