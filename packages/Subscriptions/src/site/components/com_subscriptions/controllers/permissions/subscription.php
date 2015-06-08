<?php 
/**
 * @category	Com_Subscriptions
 * @package		Controller
 * @copyright   (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://www.GetAnahita.com
 */

/**
 * Subscription Controller
 * 
 * @package	Com_Subscriptions
 * @category		Controller
 */
class ComSubscriptionsControllerPermissionSubscription extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * only admins can browse
     */    
    public function canBrowse()
    {
        return $this->canAdminister();
    }
    
    /**
     * Can't be guest
     * 
     * @return boolean
     */
    public function canRead()
    {
        return !get_viewer()->guest();
    }
    
    /**
     * (non-PHPdoc)
     * @see ComBaseControllerPermissionDefault::canAdd()
     */
    public function canAdd()
    {
        return  $this->getOrder() instanceof ComSubscriptionsDomainEntityOrder && 
                $this->getOrder()->canProcess();
    }
}