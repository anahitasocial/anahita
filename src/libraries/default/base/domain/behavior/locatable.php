<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Locatable Behavior
 * 
 * Adds the method getURL that return a unique resource location for an entity
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorLocatable extends AnDomainBehaviorAbstract
{
	/**
	 * Returns the resource URL
	 * 
	 * @return string
	 */
	public function getURL()
	{
		if ( !isset($this->_url) ) 
		{
			$this->_url = 'index.php?option='.$this->component.'&view='.$this->_mixer->getIdentifier()->name;
			
			if ( $this->_mixer->id )
				$this->_url .= '&id='.$this->_mixer->id;
		}
	
		return $this->_url;
	}
}