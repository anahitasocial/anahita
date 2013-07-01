<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Components
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Component object
 *
 * @category   Anahita
 * @package    Lib_Components
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibComponentsDomainRepositoryComponent extends AnDomainRepositoryDefault
{
	/**
	 * Instantiate a new entity based on the passed data This method is called from _create.
	 *
	 * @param string $identifier The identifier of the entity to instantiate
	 * @param array  $data       The raw data
	 *
	 * @return AnDomainEntityAbstract
	 */
	protected function _instantiateEntity($identifier, $data)
	{
		$identifier = clone $identifier;
		$identifier->package = str_replace('com_','', $data['option']);
		register_default(array('identifier'=>$identifier, 'default'=>array('ComComponentsDomainEntityComponent','LibComponentsDomainEntityComponent')));
		return clone AnDomain::getRepository($identifier)->getClone();		
	}	
}