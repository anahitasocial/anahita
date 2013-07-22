<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Page Authorizer
 *
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesDomainAuthorizerPage extends ComMediumDomainAuthorizerDefault
{
	/**
	 * Check if a node authroize being updated
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	protected function _authorizeEdit($context)
	{
		$ret = parent::_authorizeEdit($context);

		if( $ret === false )
		{
			if ( $this->_entity->isOwnable() )
			{						
				$resource = $this->_entity->component.':'.KInflector::pluralize($this->_entity->getIdentifier()->name);
				
				if( $this->_entity->owner->allows( $this->_viewer, 'com_pages:edit:pages' ) )
					return true; 			
			}
		}
		
		return $ret;
	}
}