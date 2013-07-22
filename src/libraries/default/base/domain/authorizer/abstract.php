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
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Authorizer class.
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Authorizer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class LibBaseDomainAuthorizerAbstract extends KObject 
{
	/**
	 * Viewer
	 * 
	 * @var ComPeopleDomainEntityPerson
	 */
	protected $_viewer;
	
	/**
	 * The authorizing object
	 * 
	 * @var AnDomainEntityAbstract
	 */
	protected $_entity;
	
	/**
	 * Authorization Constants. If AUTH_NOT_IMPLEMENTED is returned then
	 * chain will continue its search
	 */
	const AUTH_PASSED   = true;
	const AUTH_FAILED	= false;
	const AUTH_NOT_IMPLEMENTED = -9999;
		
    /**
     * Executes an authorization action with the passed arguments
     * 
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     * 
     * @return boolean     Can return both true or false.  
     */
	final public function execute($action, KCommandContext $context)
	{
		$method = '_'.KInflector::variablize('authorize.'.$action);
		
		if ( method_exists($this, $method) ) 
		{
			$this->_entity = $context->mixer;
			$this->_viewer = $context->viewer;
			return $this->$method($context);
		}
		
		return self::AUTH_NOT_IMPLEMENTED;
	}
}