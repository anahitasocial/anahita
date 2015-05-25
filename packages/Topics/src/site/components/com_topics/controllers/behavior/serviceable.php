<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Adds a BREAD action to the controller. It also mixes other behaviors 
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsControllerBehaviorServiceable extends LibBaseControllerBehaviorServiceable
{ 

    /**
     * Service Browse
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainQuery
     */
    protected function _actionBrowse(KCommandContext $context)
    {
    	if( !$context->query )
        {
           $context->query = $this->getRepository()->getQuery(); 
        }
    	
    	$query = $context->query;
        
        if( $this->q )
        {
            $query->keyword = $this->getService('anahita:filter.term')->sanitize($this->q);
        }
        
        $query->limit($this->limit, $this->start);
    
        return $this->getState()->setList($query->toEntityset())->getList();
    }
}