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
 * Parentable Behavior
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorParentable extends AnDomainBehaviorAbstract
{
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
	    $config->append(array(
	       'polymorphic' => true     
        ));
	    
	    $relationship['polymorphic'] = $config->polymorphic;
	    
		//if parent is set, then set the base parent
		if ( isset($config['parent']) ) 
		{
			$identifier 	   = clone $config->service_identifier;
			$identifier->path  = array('domain','entity');
			$identifier->name  = $config['parent'];
			$relationship['parent'] = $identifier;
			unset($config['parent']);
			$config->append(array(
			    'aliases' => array($identifier->name => 'parent')       
            ));
			
		}

		$config->append(array(
			'relationships' => array(
				'parent'	=> $relationship
			)
		));
		
		parent::_initialize($config);
	}	
}