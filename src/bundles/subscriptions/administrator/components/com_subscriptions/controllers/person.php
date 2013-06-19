<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Person Controller
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsControllerPerson extends ComBaseControllerService
{

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{        
		parent::_initialize($config);        		
	}
		
	/**
	 * Browse Action
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	public function _actionBrowse($context)
	{
		$context->query = $this->getRepository()->getQuery()->keyword($this->search);
		
		if ( $this->package ) 
		{
		    //load the package object
		    $this->getService('repos://admin/subscriptions.package');
			$context->query->where('subscription.package.id','=', $this->package);		
		}
				
		parent::_actionBrowse($context);
	}	
	
	/**
	 * Browse Action
	 * 
	 * @param  KCommandContext $context
     * 
	 * @return void
	 */
	public function _actionRead($context)
	{
		$person = parent::_actionRead($context);
        
		$this->packages = $this->getService('repos:subscriptions.package')->fetchSet();
	}
}