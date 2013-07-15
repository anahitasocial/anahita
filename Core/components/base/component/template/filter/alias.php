<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Alias Filter
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateFilterAlias extends KTemplateFilterAlias
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_alias_read = array_merge($this->_alias_read, array(
		    '@title('       => 'JFactory::getDocument()->setTitle(',
		    '@description(' => 'JFactory::getDocument()->setDescription(',			
			'@controller(' 	=> '$this->renderHelper(\'controller.getController\',',
			'@view('	   	=> '$this->renderHelper(\'controller.getView\',',
			'@previous('   	=> '$this->getHelper(\'previous\')->load(',
			'@template('   	=> '$this->getView()->load(',
			'@route('	   	=> '$this->getView()->getRoute(',
			'@html(\''     	=> '$this->renderHelper(\'com:base.template.helper.html.',
		));
	}	
}