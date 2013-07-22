<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Dashboard
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Dashboard HTML View
 *
 * @category   Anahita
 * @package    Com_Dashboard
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComDashboardViewDashboardHtml extends ComBaseViewHtml
{	
	/**
	 * Prepare default layout
	 * 
	 * @return void
	 */
	protected function _layoutDefault()
	{	
		$this->set('gadgets',     new LibBaseTemplateObjectContainer());
		$this->set('composers',   new LibBaseTemplateObjectContainer());
		$context 		  = new KCommandContext();		
		$context->actor	  = $this->viewer;
		$context->gadgets     = $this->gadgets;
		$context->composers   = $this->composers;
				
		//make all the apps to listen to dispatcher
		$apps = $this->getService('repos:apps.app')->getQuery()
		    ->order('ordering','ASC')
			->fetchSet();
		
		$apps->registerEventDispatcher($this->getService('anahita:event.dispatcher'));
				
		$this->getService('anahita:event.dispatcher')->dispatchEvent('onDashboardDisplay', $context);				
	}
}