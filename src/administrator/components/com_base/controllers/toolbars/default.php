<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Resource Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarDefault extends KControllerToolbarAbstract
{
    /**
     * Push the toolbar into the view
     * .
     * @param	KEvent	A event object
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        KService::set('com:controller.toolbar', $this);
        $event->getPublisher()->getView()->toolbar = $this;
    }       
    
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $package = KInflector::humanize($this->getIdentifier()->package);
        $name    = KInflector::humanize(KInflector::pluralize($this->getName()));
        
        $config->append(array(
            'title'  => $package.' - '.$name
        ));

        parent::_initialize($config);
    }
         
    /**
     * Add default toolbar commands and set the toolbar title
     * .
     * @param	KEvent	A event object
     */
    public function onAfterControllerRead(KEvent $event)
    {
        $name = ucfirst($this->getController()->getIdentifier()->name);
        
        if( $this->getController()->getState()->isUnique() )
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
        if( $this->getController()->canAdd() )
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
     * Parameters toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandParameters(KControllerToolbarCommand $command)
    {
        $option = $this->_identifier->package;
        
		JHTML::_('behavior.modal');
		
        $command->append(array(
            'width'   => '640',
            'height'  => '480',
            'href'	  => ''
        ))->append(array(
            'attribs' => array(
                'class' => array('modal'),
                'href'  => 'index.php?option=com_config&tmpl=component&controller=component&component=com_'.$option,
                'rel'   => '{handler: \'iframe\', size: {x: '.$command->width.', y: '.$command->height.'}}'
            )
        ));
    }
}