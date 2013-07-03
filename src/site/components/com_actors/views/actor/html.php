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
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Actor View (Profile View)
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsViewActorHtml extends ComBaseViewHtml
{
   /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'template_paths' => array(dirname(__FILE__).'/html')
		));
		
		parent::_initialize($config);
		
		$config->append(array(
			'template_paths' => array(JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate().'/html/com_actors/actor')			
		));
	}
	
	/**
	 * Default layout
	 * 
	 * @return void
	 */
	protected function _layoutDefault()
	{
		$context = new KCommandContext();
		$context->gadgets 		= new LibBaseTemplateObjectContainer();
		$context->actor	  		= $this->_state->getItem();
		$context->composers     = new LibBaseTemplateObjectContainer();
		$context->commands 		= $this->getTemplate()->renderHelper('toolbar.commands', 'toolbar');
		$context->profile       = new KConfig();
        $context->gadgets->insert('socialgraph', array(
                'title' 		=> translate(array('COM-ACTORS-GADGET-LABEL-SOCIALGRAPH','COM-'.strtoupper($this->getIdentifier()->package).'-GADGET-LABEL-SOCIALGRAPH')),                
                'url'			=> $context->actor->getURL().'&get=graph&layout=gadget',
                'title_url'		=> $context->actor->getURL().'&get=graph&type=followers'
        ));
				
        if ( $this->_state->getItem()->authorize('access') )
        {        	
        	$this->_state->getItem()->components->registerEventDispatcher( $this->getService('anahita:event.dispatcher'));
        	
        	$this->getService('anahita:event.dispatcher')->dispatchEvent('onProfileDisplay', $context);
        	        	       
            $this->getService('anahita:event.dispatcher')->dispatchEvent('onProfileDisplay', $context);
            
            dispatch_plugin('profile.onDisplay', array('actor'=>$this->_state->getItem(), 'profile'=>$context->profile));
            
            $this->profile = $context->profile;
            
            if ( count($context->profile) > 0 )
                $context->gadgets->insert('information', array(
                        'title' 		=> translate(array('COM-ACTORS-GADGET-LABEL-ACTOR-INFO','COM-'.strtoupper($this->getIdentifier()->package).'-GADGET-LABEL-ACTOR-INFO')),
                        'content'		=> $this->load('info')
                ));
        }
                
        $context->gadgets->sort(array('stories','information'));                
                     
		$this->set(array(
            'commands'  => $context->commands, 
            'gadgets'   => $context->gadgets,
            'composers' => $context->composers            
        ));
	}
	
	/**
     * Default Badge
     *
     * @return
     */
	protected function _layoutBadge()
	{
    	$context->commands = $this->getTemplate()->renderHelper('toolbar.commands', 'toolbar');
		
		$this->set(array(
            'commands'  => $context->commands           
        ));
	}
}