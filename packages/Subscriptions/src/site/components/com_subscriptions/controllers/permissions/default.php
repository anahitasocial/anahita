<?php
/**
 * @category    Com_Subscriptions
 * @package     Controller
 * @copyright   (C) 2008 - 2015 rmdStudio Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://www.GetAnahita.com
 */

/**
 * Subscription Controller
 * 
 * @package     Com_Subscriptions
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionDefault extends LibBaseControllerPermissionDefault
{
    /**
     * Authorize if viewer can add
     *
     * @return boolean
     */    
    public function canAdd()
    {
        $viewer = get_viewer();
        
        if( $viewer->admin() )
        {
            return true;
        }
        
        return false;
    }
}
    