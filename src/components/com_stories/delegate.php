<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story App Delegate
 *   
 * @category   Anahita
 * @package    Com_Stories
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDelegate extends ComAppsDomainDelegateDefault
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
            'priority'            => -PHP_INT_MAX,
            'assignment_option'   => self::ASSIGNMENT_OPTION_ALWAYS       
        ));
        
        return parent::_initialize($config);
    }   
     
    /**
     * @{inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        $controller = $this->getService('com://site/stories.controller.story');        
        $content    = $controller;
                                                
        if ( $mode == 'profile' ) 
        {
            $controller->oid($actor->id)->view('stories');
                                        
            $gadgets->insert('stories', array(
                    'title'      => JText::_('COM-STORIES-GADGET-TITLE-STORIES'),
                    'show_title' => get_viewer()->guest(),
                    'content'    => $content
            ));
        } else 
        {
            $controller->view('stories')->filter('leaders');
            
            $gadgets->insert('stories', array(
                    'title' 		=> JText::_('COM-STORIES-GADGET-TITLE-STORIES'),
                    'show_title'    => get_viewer()->guest(),
                    'content'       => $content
            ));
        }
    }
    
    /**
     * @{inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ( $actor->authorize('action','com_stories:story:add') )
        {
            $composers->insert('stories',array(
                'title'	       => JText::_('COM-STORIES-COMPOSER-STORY'),
                'placeholder'  => JText::_('COM-STORIES-COMPOSER-PLACEHOLDER'),
                'url'      => 'option=com_stories&layout=composer&view=story&oid='.$actor->id
            ));
        }
    }
        
	/**
	 * Return a set of resources and type of operation on each resource
	 *
	 * @return array
	 */
	public function getResources()
	{
	    return array(
	        'story' => array('add','addcomment')	            
	    );
	}	
}