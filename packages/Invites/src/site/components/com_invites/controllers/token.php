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
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'toolbars' => null,
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * Token Read
     * 
     * @param KCommandContext $context
     */   
    protected function _actionRead(KCommandContext $context)
    { 
        if ( $this->token ) 
        {
            $token = $this->getRepository()->find(array('value'=>$this->token));
            $this->getToolbar('menubar')->setTitle(null);
            
            if ( !$token || !isset($token->inviter)) {
                throw new LibBaseControllerExceptionNotFound('Token not found');
            }
                        
            if ( $this->viewer->guest()  ) {
                KRequest::set('session.invite_token', $token->value);                               
            }

            $this->setItem($token);           
        }
        else
        {
            $service = pick($this->service, 'facebook');                        
            $token   = $this->getRepository()->getEntity()->reset();
            KRequest::set('session.invite_token', $token->value);
            $this->getView()                
                ->value($token->value);
        
            return $this->getView()->display();
        }
    }
    
    /**
     * Store a token for a service
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $data  = $context->data;
        $value = KRequest::get('session.invite_token', 'string', null);
        
        if( empty($data->value) || $value != $data->value) {
            throw new LibBaseControllerExceptionBadRequest('Invalid token signature');
        }
        
        KRequest::set('session.invite_token', null);
        
        $token = $this->getRepository()->getEntity(array(
                'data'=> array(
                        'value'	      => $value,
                        'inviter'     => get_viewer(),
                        'serviceName' => 'facebook'
                )
        ));
        
        if ( !$token->save() ) {
            throw new LibBaseControllerExceptionInternal();
        }
    }
}