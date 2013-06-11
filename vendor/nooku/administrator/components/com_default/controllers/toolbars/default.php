<?php
/**
 * @version   	$Id: default.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Toolbar
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarDefault extends KControllerToolbarDefault
{
	/**
	 * Push the toolbar into the view
	 * .
	 * @param	KEvent	A event object
	 */
    public function onBeforeControllerGet(KEvent $event)
    {   
        $event->getPublisher()->getView()->toolbar = $this;
    }
	
	/**
	 * Add default toolbar commands and set the toolbar title
	 * .
	 * @param	KEvent	A event object
	 */
    public function onAfterControllerRead(KEvent $event)
    { 
        $name = ucfirst($this->getController()->getIdentifier()->name);
            
        if($this->getController()->getModel()->getState()->isUnique()) 
        {        
            $saveable = $this->getController()->canEdit();
            $title    = 'Edit '.$name;
        } 
        else 
        {
            $saveable = $this->getController()->canAdd();
            $title    = 'New '.$name;  
        }
            
        if($saveable)
        {
            $this->setTitle($title)
                 ->addCommand('save')
                 ->addCommand('apply');
        }
                   
        $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));       
    }
      
    /**
	 * Add default toolbar commands
	 * .
	 * @param	KEvent	A event object
	 */
    public function onAfterControllerBrowse(KEvent $event)
    {    
        if($this->getController()->canAdd()) 
        {
            $identifier = $this->getController()->getIdentifier();
            $config     = array('attribs' => array(
                    		'href' => JRoute::_( 'index.php?option=com_'.$identifier->package.'&view='.$identifier->name)
                          ));
                    
            $this->addCommand('new', $config);
        }
            
        if($this->getController()->canDelete()) {
            $this->addCommand('delete');    
        }
    }
       
    /**
     * Enable toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-publish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:1}'
            )
        ));
    }

    /**
     * Disable toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-unpublish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:0}'
            )
        ));
    }

    /**
     * Export toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(KControllerToolbarCommand $command)
    {
        //Get the states
        $states = $this->getController()->getModel()->getState()->toArray();

        unset($states['limit']);
        unset($states['offset']);

        $states['format'] = 'csv';

        //Get the query options
        $query  = http_build_query($states);
        $option = $this->getIdentifier()->package;
        $view   = $this->getIdentifier()->name;

        $command->append(array(
            'attribs' => array(
                'href' =>  JRoute::_('index.php?option=com_'.$option.'&view='.$view.'&'.$query)
            )
        ));
    }

    /**
     * Modal toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandModal(KControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;

        $command->append(array(
            'width'   => '640',
            'height'  => '480',
            'href'	  => ''
        ))->append(array(
            'attribs' => array(
                'class' => array('modal'),
                'href'  => $command->href,
                'rel'   => '{handler: \'iframe\', size: {x: '.$command->width.', y: '.$command->height.'}}'
            )
        ));
    }
}