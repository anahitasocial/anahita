<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Invite Default Contorller
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesControllerConnection extends ComInvitesControllerDefault
{
    /**
     * Context
     * 
     * @param KCommandContext $context
     * 
     * @return void 
     */
    protected function _actionBrowse(KCommandContext $context)
    {
       $service = pick($this->service, 'facebook');       
       $this->getService('repos://site/connect.session');
       $service = $this->viewer->sessions->$service;
       if ( !empty($service) ) {
          $this->_state->setList($service->getConnections());
       }
       $this->service = $service;
       return $service->getConnections();
    }
}