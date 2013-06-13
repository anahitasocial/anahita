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

class ComInvitesControllerEmail extends ComInvitesControllerDefault
{		
	/**
	 * Read
	 * 
	 * @param KCommandContext $contxt
	 * 
	 * @return void
	 */	
	protected function _actionInvite($context)
	{	
		$data = $context->data;		
		$siteConfig	= JFactory::getConfig();
		
		$emails = KConfig::unbox($data['email']);
		settype($emails, 'array');
		
		foreach($emails as $email) 
		{
			if($email)
			{
				$token = $this->getService('repos://site/invites.token')->getEntity(array(
					'data'=> array(
						'inviter' => get_viewer(),
						'service' => 'email' 
					)
				));
								
				$message = $this->getView()
								->inviteUrl($token->getURL())
								->siteName($siteConfig->getValue('sitename'))
								->sender($this->viewer)
								->layout('_message');

		        $mailer = JFactory::getMailer();
				$mailer->isHTML(true);
		        $mailer->addRecipient($email);
		        $mailer->setSubject(JText::sprintf('COM-INVITES-MESSAGE-SUBJECT', $siteConfig->getValue('sitename')));	
				$mailer->setBody($message);
				$mailer->addReplyTo(array($siteConfig->getValue('mailfrom'), $siteConfig->getValue('sitename')));
				$mailer->send();
				$token->save();
			}
		}
	}
	
	/**
	 * Return the email adapter
	 * 
	 * @return mixed
	 */	
	protected function getAdapter()
	{
		if ( !isset($this->_adapter) ) {
			$this->_adapter = $this->getService('com://site/invites.adapter.email');			
		}
		
		return $this->_adapter;
	}	
}