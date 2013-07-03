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
class ComInvitesControllerToken extends ComBaseControllerService
{	
    /**
     * Token Read
     * 
     * @param KCommandContext $context
     */   
    protected function _actionRead(KCommandContext $context)
    { 
        if ( $this->token ) {
            $this->execute('validate', $context);
        }
        else
        {
            $token = $this->getRepository()->getEntity()->reset();
            KRequest::set('session.invite_token', $token->value);
            $this->getView()
                ->url((string)JRoute::_($token->getURL()))
                ->value($token->value);
        
            return $this->getView()->display();
        }
    }
	
	/**
	 * Validates a token
	 *
	 * @return string
	 */	
	protected function _actionValidate(KCommandContext $context)
	{
		$token = $this->getRepository()->find(array('value'=>$this->token));
		
		if ( $token && ($token->used == 0 || 
		        $token->serviceName == 'facebook')) 
		{
			if( $this->viewer->guest() )
			{
				KRequest::set('session.invite_token', $token->value);
				$context->response->setRedirect(JRoute::_('option=people&view=person&layout=add'));
			}
			else {
			    $context->response->setRedirect(JRoute::_($token->inviter->getURL()));
			}
		} 
		else 
		{
		    throw new LibBaseControllerExceptionNotFound('Token not found');					
		}
	}
}