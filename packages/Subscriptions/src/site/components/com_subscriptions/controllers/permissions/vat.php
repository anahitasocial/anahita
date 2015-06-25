<?php 
/**
 * @category    Com_Subscriptions
 * @package     Controller
 * @copyright   (C) 2008 - 2015 rmdStudio Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://www.GetAnahita.com
 */

/**
 * Vat Controller
 * 
 * @package     Com_Subscriptions
 * @category    Controller
 */
class ComSubscriptionsControllerPermissionVat extends ComSubscriptionsControllerPermissionDefault
{
    /**
     * Authorize if viewer can Browse
     *
     * @return boolean
     */    
    public function canBrowse()
    {
        return $this->canAdminister();
    }
    
    /**
     * Authorize if viewer can read
     *
     * @return boolean
     */    
    public function canRead()
    {
        return $this->canAdminister();
    }
}
    