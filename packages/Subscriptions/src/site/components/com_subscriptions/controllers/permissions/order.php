<?php 
/**
 * @category    Com_Subscriptions
 * @package     Controller
 * @copyright   (C) 2008 - 2015 rmdStudio Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://www.GetAnahita.com
 */

/**
 * Package Controller
 * 
 * @package     Com_Subscriptions
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionOrder extends ComSubscriptionsControllerPermissionDefault
{
    
    /**
     * Authorize if viewer can browse
     *
     * @return boolean
     */         
    public function canBrowse()
    {
        $viewer = get_viewer();
        
        if( $viewer->guest() )
        {
            return false;
        }
        
        if( !$viewer->admin() && !isset($this->actor->id) )
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Authorize if viewer can read
     *
     * @return boolean
     */
    public function canRead()
    {
       $viewer = get_viewer();
        
        if($viewer->admin())
        {
            return true;
        }

        if( $this->getItem()->actorId == $viewer->id )
        {
            return true;
        }
        
        return false;  
    }
    
    /**
     * Authorize if viewer can add
     *
     * @return boolean
     */    
    public function canAdd()
    {
        return false;
    }
    
    /**
     * Authorize if viewer can edit
     *
     * @return boolean
     */
    public function canEdit()
    {
        return false;
    }        
}    