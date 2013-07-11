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

class ComInvitesControllerFacebook extends ComInvitesControllerDefault
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
        parent::__construct($config);
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
            'request' => array('limit'=>50,'offset'=>0)
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * Read
     *
     * @param KCommandContext $contxt
     *
     * @return void
     */
    protected function _actionRead($context)
    {
        $fbfriends = $this->getService('com://site/invites.domain.entityset.fbfriend', 
                    array('actor'=>$this->viewer));
        
        $fbfriends->limit($this->limit, $this->offset);
        
        $this->_state->setList($fbfriends);
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