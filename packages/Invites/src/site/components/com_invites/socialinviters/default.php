<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage SocialInviter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Social Inviter
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage SocialInviter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesSocialinviterDefault extends KObject
{
    /**
     * Return the social service name [e.g. facebook, twitter and etc]
     * 
     * @var string
     */
    protected $_service_name;
        
    /**
     * The inviter person
     * 
     * @var ComPeopleDomainEntityPerson
     */
    protected $_inviter;
    
    /**
     * Inviter oauth sesssion
     * 
     * @var ComConnectDomainEntitySession
     */
    protected $_oauth_session;
    
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
        
        $this->_service_name = $config->service_name;
                
        $this->_inviter      = $config->inviter;
        
        if ( !$config->inviter ) {
            throw new \InvalidArgumentException('Missing argument inviter.');
        }
        
        $this->_oauth_session = $this->getService('repos://site/connect.session')
                    ->fetch(array('owner'=>$this->_inviter, 'api'=>$this->_service_name));
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
            'service_name'    => $this->getIdentifier()->name
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * Retunr whether the user can invite or not. That depends if the inviter has
     * a valid oauth session with the service
     * 
     * @return boolean
     */
    public function canInvite()
    {
        return !empty($this->_oauth_session);
    }
    
    /**
     * Return an array of oauth users
     * 
     * @return array
     */
    public function getInvitables()
    {
        $this->getConnections();
        die;
        if ( $this->getInvite() ) 
        {
            
        }
        return array('d','d');
    }
    
    /**
     * 
     * @return 
     */
    protected function getConnections()
    {
        $cache = JFactory::getCache((string) $this->getIdentifier(), '');
        $key   = 'connections_'.$this->_oauth_session->id;
        $data  = $cache->get($key);
        if ( !$data ) 
        {
            $data = $this->_getConnections();                
            $ids     = array();
            $users   = array();
            foreach($data as $i => $user) {
                $user   = $this->_mapAttributes($user);
                $ids[]  = $user['id'];
                $users[$user['id']] = $user;
            }
            $sessions = $this->getService('repos://site/connect.session')
                ->getQuery()
                        ->where(array(
                            'profileId'=> $ids,
                            'api'      => $this->_service_name))
                        ->fetchSet()
                ;
            foreach($sessions as $session) {
                unset($users[$session->profileId]);
            } 
            $array = array();
            $array['actor_ids'] = $sessions->owner->id;
            $array['users']     = array_values($users); 
            $cache->store($array, $key);
        }        
    }
        
    /**
     * Return an array of data
     *
     * @return array
     */
    protected function _getConnections()
    {        
        $data = $this->_oauth_session->getApi()->get('/me/friends');
        return $data['data'];
    }
    
    /**
     * Maps the serivce user attribures into oatuh user attributes
     * 
     * @return array
     */
    protected function _mapAttributes($user)
    {        
        $data           = array();
        $data['id']     = $user['id'];
        $data['name']   = $user['name'];
        $data['avatar'] = 'https://graph.facebook.com/'.$user['id'].'/picture';
        return $data;
    }
    

}