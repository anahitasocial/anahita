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

class ComInvitesControllerFacebook extends ComBaseControllerResource
{
    /**
     * Read
     *
     * @param KCommandContext $contxt
     *
     * @return void
     */
    protected function _actionRead($context)
    {       
        $this->social_inviter = $this->getService('com://site/invites.socialinviter.facebook', array(
                'inviter' => get_viewer()
        ));
//         $context->response->setContent('dadfas');
        return;
         
        foreach($socialInviter->getInvitables() as $user) 
        {
              print $user;  
        }
        //print $socialInviter;
        die;
        die; 
        $this->users = $this->adapter->getInvitables()
                            ->filter('name', $this->q)
                            ->limit($this->start, $this->limit);
    }    
        
	/**
	 * Invite
	 * 
	 * @param KCommandContext $contxt
	 * 
	 * @return void
	 */		
	protected function _actionInvite($context)
	{
		$value = KRequest::get('session.invite_token', 'string', null);
		
		if ( !$value ) {
			return false;
		}
		
		if($value != $context->data->token)
			return false;	
		
		KRequest::set('session.invite_token', null);
		
		$token = $this->getService('repos://site/invites.token')->getEntity(array(
			'data'=> array(
				'value'	      => $value,
				'inviter'     => get_viewer(),
				'serviceName' => 'facebook' 
			)
		));

		$token->save();
	}
}