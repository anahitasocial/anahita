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
class ComSubscriptionsControllerPermissionCoupon extends LibBaseControllerPermissionDefault
{
    /**
     * Authorize if viewer can change subscription
     *
     * @return boolean
     */    
    public function canAdminister()
    {
        $viewer = get_viewer();
            
        return $viewer->admin() ? true : false;
    }
    
    /**
     * Authorize if viewer can change subscription
     *
     * @return boolean
     */    
    public function canAdd()
    {
        return $this->canAdminister();
    }
    
    /**
     * Authorize if viewer can change subscription
     *
     * @return boolean
     */    
    public function canEdit()
    {
        return $this->canAdminister();
    }
}
    