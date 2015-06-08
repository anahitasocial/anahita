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
class ComSubscriptionsControllerPermissionPackage extends ComSubscriptionsControllerPermissionDefault
{
    
    
     /**
     * Authorize if viewer can add subscriber
     *
     * @return boolean
     */    
    public function canAddsubscriber()
    {
        return $this->canAdminister();
    } 
    
    /**
     * Authorize if viewer can delete subscriber
     *
     * @return boolean
     */    
    public function canDeletesubscriber()
    {
        return $this->canAdminister();
    }
    
    /**
     * Authorize if viewer can change subscription
     *
     * @return boolean
     */    
    public function canChangesubscription()
    {
        return $this->canAdminister();
    }
}
    