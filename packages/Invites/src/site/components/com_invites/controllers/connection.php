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
       $serviceType = pick($this->service, 'facebook');
       
       if ( !ComConnectHelperApi::enabled($serviceType) ) 
       {
           throw new LibBaseControllerExceptionBadRequest('Service is not enabled');
       }
       
       $this->getService('repos://site/connect.session');
       
       $service = $this->viewer->sessions->$serviceType;
       
       if ( !empty($service) ) 
       {
       		try 
          	{
              $this->_state->setList($service->getFriends());
          	} 
          	catch( Exception $e ) 
         	{
            	$session = $this->viewer->sessions->find(array('api'=>'facebook'));
              
            	if ($session)
            	{ 
              		$session->delete()->save();
            	}
              
              	$service = null;
          	}
       }
       else 
       {
			$service = null;	
       }
       
       $this->service = $service;
    }
}