<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Medium Authorizer
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumDomainAuthorizerComponent extends LibBaseDomainAuthorizerAbstract
{    
	/**
	 * Authorizes whether the viewer can pubilsh anything within this component or not
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return boolean
	 */
	protected function _authorizeAction(KCommandContext $context)
	{
		$method = '_authorize'.ucfirst($context->action);
		$ret    = self::AUTH_NOT_IMPLEMENTED;
		if ( method_exists($this, $method) ) {
			$ret = $this->$method($context->resource);
		} else 
		{
			if ( $context->actor ) 
			{
			    //check if it's enabled and assigned
			    if ( $this->_entity->isAssignable() ) {
			        $ret = $this->_entity->activeForActor($context->actor);
			    }
			}
		}
		return $ret;
	}
}