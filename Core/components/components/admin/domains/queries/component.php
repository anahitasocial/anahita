<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

KService::get('koowa:loader')->loadIdentifier('com://site/components.domain.behavior.assignable');

/**
 * Component query
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsDomainQueryComponent extends AnDomainQuery
{
    /**
     * Called before the update query 
     * 
     * @param KCommandContext $context
     * 
     * @return void;
     */
    protected function _beforeQueryUpdate(KCommandContext $context)
    {
        $this->option($this->getService('com://admin/components.domain.set.assignablecomponent')->option);
    }
    
	/**
	 * Provides option to return assignable/nonassigable components
	 * 
	 * @return void
	 */
	protected function _beforeQuerySelect(KCommandContext $context)
	{		
	    if ( $this->assignable ) {
	        $this->option($this->getService('com://admin/components.domain.set.assignablecomponent')->option);
	    }        				
	}
}