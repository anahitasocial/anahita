<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Topic Toolbar 
 *
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsControllerToolbarTopic extends ComMediumControllerToolbarDefault
{	    
	/**
	 * Add Admin Commands for an entity
     * 
	 * @return void
	 */
	public function addAdministrationCommands()
	{
	    $this->addCommand('sticky');
	
	    parent::addAdministrationCommands();
	}
		
	/**
	 * Customize the sticky command
	 *
	 * @param LibBaseTemplateObject $command Command Object
	 *
	 * @return void
	 */	
	protected function _commandSticky($command)
	{
		$entity = $this->getController()->getItem();
		
		$label  = ( $entity->isSticky ) ? JTEXT::_('COM-TOPICS-TOPIC-REMOVE-STICKY') : JTEXT::_('COM-TOPICS-TOPIC-MAKE-STICKY');
		
		$command
		->append(array('label'=>$label))
		->href( $entity->getURL().'&action=sticky&is_sticky='.($entity->isSticky ? 0 : 1) )
		->setAttribute('data-trigger','Submit');
	}
}