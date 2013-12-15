<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Medium Permission
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComMediumControllerPermissionAbstract extends LibBaseControllerPermissionDefault
{
	/**
	 * Authorize Browse
	 *
	 * @return boolean
	 */
	public function canBrowse()
	{	
		$viewer = get_viewer();
		
		if( $viewer->id )
		{
			if($this->filter == 'leaders' && $viewer->id == $this->actor->id)
				return true;
			elseif ($this->actor->id)
				return true;
			
		}
		elseif( $this->actor->id )
			return true;
		
		return false;	
	}
	
	/**
	 * Authorize Read
	 *
	 * @return boolean
	 */
	public function canRead()
	{
		$actor		= pick($this->actor, get_viewer());		
	
		//if repository is ownable then ask the actor if viewer can publish things
		if ( in_array($this->getRequest()->get('layout'), array('add', 'edit', 'form','composer'))) 
		{
		    if ( $this->getItem() ) {		        
		       $result = $this->canEdit();
		    } else {
		       $result = $this->canAdd();
		    }
		    
		    return $result;
		}
	
		if ( !$this->getItem() )
			return false;
	
		//check if an entiy authorize access
		return $this->getItem()->authorize('access');
	}
	
	/**
	 * Authorize if viewer can add
	 *
	 * @return boolean
	 */
	public function canAdd()
	{
		$actor = $this->actor;
	
		if ( $actor )
		{
			$action  = 'com_'.$this->_mixer->getIdentifier()->package.':'.$this->_mixer->getIdentifier()->name.':add';
			$ret = $actor->authorize('action',$action);
			return $ret !== false;
		}
		 
		return false;
	}
	
	/**
	 * Authorize Read
	 *
	 * @return boolean
	 */
	public function canEdit()
	{
		if ( $this->getItem() ) {
			return $this->getItem()->authorize('edit');
		}
	
		return false;
	}
	
	/**
	 * If an app is not enabled for an actor then don't let the viewer to see it
	 *
	 * @param string $action Action name
	 *
	 * @return boolean
	 */
	public function canExecute($action)
	{	
		//check if viewer has access to actor
		if ( $this->isOwnable() && $this->actor )  {
			if ( $this->actor->authorize('access') === false ) {
				return false;
			}
		}
	
		return parent::canExecute($action);
	}	
}