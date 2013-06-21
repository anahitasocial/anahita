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
	 * Return a token
	 *
	 * @return string
	 */
	protected function _actionGet(KCommandContext $context)
	{
		if ( $this->token ) 
		{
			$this->execute('validate', $context);
		} 
		else 
		{
			$token = $this->getRepository()->getEntity()->reset();		
			KRequest::set('session.invite_token', $token->value);
			$this->getView()
				->url(JRoute::_('index.php?'.$token->getURL()))
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
		$token = $this->getRepository()->getQuery()->fetch(array('value'=>$this->token));
		
		if ( $token && ($token->used == 0 || $token->serviceName == 'facebook')) 
		{
			if(is_guest(get_viewer()))
			{
				KRequest::set('session.invite_token', $token->value);
				$this->setRedirect('index.php?option=com_user&view=register');
			}
			else 
				$this->setRedirect($token->inviter->getURL());
		} 
		else 
		{
			$context->setError(new KHttpException(
                    'Looks like you have an invalid invitation', KHttpResponse::NOT_FOUND
			));
			
			return false;			
		}
	}
}