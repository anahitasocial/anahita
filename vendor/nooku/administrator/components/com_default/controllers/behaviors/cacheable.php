<?php
/**
 * @version     $Id: cacheable.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Controller Cacheable Behavior
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerBehaviorCacheable extends KControllerBehaviorAbstract
{
	/**
	 * The cached state of the resource
	 * 
	 * @var boolean
	 */
	protected $_output = ''; 
	
	/**
	 * Fetch the unrendered view data from the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	void	
	 */
	protected function _beforeControllerGet(KCommandContext $context)
	{ 
	    $view   = $this->getView();
	    $cache  = JFactory::getCache($this->_getGroup(), 'output');
        $key    = $this->_getKey();
            
        if($data = $cache->get($key))
        {
            $data = unserialize($data);
            
            //Render the view output
            if($view instanceof KViewTemplate) 
            {
                $context->result = $view->getTemplate()
                               ->loadString($data['component'], array(), false)
                               ->render();
            } 
            else $context->result = $data['component'];
            
            $this->_output = $context->result;
	    }
	}
	
	/**
	 * Store the unrendered view data in the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	void
	 */
	protected function _afterControllerGet(KCommandContext $context)
	{
	    if(empty($this->_output))
	    {
	        $view   = $this->getView();
	        $cache  = JFactory::getCache($this->_getGroup(), 'output');
	        $key    = $this->_getKey();
	  
	        $data  = array();
	   
	        //Store the unrendered view output
	        if($view instanceof KViewTemplate) {
	            $data['component'] = (string) $view->getTemplate();
	        } else {
	            $data['component'] = $context->result;
	        }
	        
	        $cache->store(serialize($data), $key);
	    }
	}
	
	/**
	 * Return the cached data after read
	 * 
	 * Only if cached data was found return it but allow the chain to continue to allow
	 * processing all the read commands
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	void
	 */
	protected function _afterControllerRead(KCommandContext $context)
	{ 
	    if(!empty($this->_output)) {
	        $context->result = $this->_output;
	    }
	}
	
	/**
	 * Return the cached data before browse
	 * 
	 * Only if cached data was fetch return it and break the chain to dissallow any
	 * further processing to take place
	 * 
	 * @param   KCommandContext	A command context object
	 * @return 	void
	 */
    protected function _beforeControllerBrowse(KCommandContext $context)
	{
	    if(!empty($this->_output)) 
	    {
	        $context->result = $this->_output;
	        return false;
	    }
	}
	
	/**
	 * Clean the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	boolean
	 */
	protected function _afterControllerAdd(KCommandContext $context)
	{
	    $status = $context->result->getStatus();
	    
	    if($status == KDatabase::STATUS_CREATED) {
	         JFactory::getCache()->clean($this->_getGroup());
	    }
	      
	    return true;
	}
	
	/**
	 * Clean the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	boolean
	 */
	protected function _afterControllerDelete(KCommandContext $context)
	{
	    $status = $context->result->getStatus();
	    
	    if($status == KDatabase::STATUS_DELETED) {
	        JFactory::getCache()->clean($this->_getGroup());
	    }
	      
	    return true;
	}
	
	/**
	 * Clean the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	boolean
	 */
	protected function _afterControllerEdit(KCommandContext $context)
	{
	    $status = $context->result->getStatus();
	    
	    if($status == KDatabase::STATUS_UPDATED) {
	        JFactory::getCache()->clean($this->_getGroup());
	    }
	      
	    return true;
	}
	
	/**
	 * Generate a cache key
	 * 
	 * The key is based on the layout, format and model state
	 *
	 * @return 	string 
	 */
	protected function _getKey()
	{
	    $view  = $this->getView();
	    $state = $this->getModel()->getState()->toArray();
	    
	    $key = $view->getLayout().'-'.$view->getFormat().':'.md5(http_build_query($state));
	    return $key;
	}
	
	/**
	 * Generate a cache group
	 * 
	 * The group is based on the component identifier
	 *
	 * @return 	string 
	 */
	protected function _getGroup()
	{ 
	    $group = $this->_mixer->getIdentifier();
	    return $group;
	}
}