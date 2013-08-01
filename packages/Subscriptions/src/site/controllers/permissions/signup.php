<?php 
/**
 * @version     $Id$
 * @category	Com_Subscriptions
 * @package		Controller
 * @copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://anahitapolis.com
 */

/**
 * Subscription Controller
 * 
 * @category	Com_Subscriptions
 * @package		Controller
 */
class ComSubscriptionsControllerPermissionSignup extends LibBaseControllerPermissionDefault
{
    /**
     * Can login
     * 
     * @return boolean
     */
    public function canLogin()
    {
        $subscriber_id = KRequest::get('session.subscriber_id', 'cmd');
        
        if ( $subscriber_id ) {
            $this->person = $this->getService('repos://site/people')->find($subscriber_id);
        }

        return !is_null($this->person);
    }
    
    /**
     * (non-PHPdoc)
     * @see LibBaseControllerPermissionAbstract::canExecute()
     */
    public function _canExecute($action)
    {
        $viewer = get_viewer();
         
        if ( $viewer->hasSubscription() )
            $ret = $this->getItem()->authorize('upgradepackage');
        else
            $ret = $this->getItem()->authorize('subscribepackage');
        
        return $ret;        
    }
}