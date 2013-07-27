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
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);;
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('com://site/mailer.controller.behavior.mailer')
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * Calls the invite action
     * 
     * (non-PHPdoc)
     * @see ComInvitesControllerDefault::_actionPost()
     */
    protected function _actionPost($context)
    {
        return $this->execute('invite', $context); 
    }
    
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
		$filter = $this->getService('koowa:filter.email');
		$emails = array_filter((array)$data['email'], function($email) use($filter) {
		    return $filter->validate($email);
		});
		$payloads = array();
		$sent_one = false;
		foreach($emails as $email) 
		{
		    $token = $this->getService('repos://site/invites.token')->getEntity(array(
					'data'=> array(
						'inviter' => get_viewer(),
						'serviceName' => 'email' 
					)
			));
		    $person  = $this->getService('repos://site/people')->find(array('email'=>$email));
		    $payload = array('email'=>$email, 'sent'=>false);
			if ( !$person && $token->save()  ) 
			{			    
			    $payload['sent']  = true;
			    $sent_one         = true;
			    $this->mail(array(
			            'subject'  => JText::sprintf('COM-INVITES-MESSAGE-SUBJECT', $siteConfig->getValue('sitename')),
			            'to'       => $email,
			            'layout'   => false,
			            'template' => 'invite',
			            'data'     => array(
			                    'invite_url' => $token->getURL(),
			                    'site_name'  => $siteConfig->getValue('sitename'),
			                    'sender'     => $this->viewer
			            )
			    ));				    
			} else {
			    $payload['person'] = $person;
			}
			$payloads[] = $payload;
		}
		$content = $this->getView()->set(array('data'=>$payloads))->display();
		$context->response->setContent($content);
		if ( $sent_one ) {
		    $this->setMessage('COM-INVITES-EMAIL-INVITES-SENT','info', false);
		}
	}
}