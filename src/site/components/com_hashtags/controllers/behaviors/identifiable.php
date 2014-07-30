<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Identifiable Behavior
 *
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Controller_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsControllerBehaviorIdentifiable extends ComBaseControllerBehaviorIdentifiable
{
	/**
	 * (non-PHPdoc)
	 * @see ComBaseControllerBehaviorIdentifiable::fetchEntity()
	 */
	public function fetchEntity(KCommandContext $context)
	{
		if($this->isDispatched() && $this->getRequest()->alias) 
			$this->setIdentifiableKey('alias');
		
		return parent::fetchEntity($context);
	}	
}