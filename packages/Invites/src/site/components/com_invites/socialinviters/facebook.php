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
class ComInvitesSocialinviterFacebook extends KObject
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
     * Users
     * 
     * @var ComConnectOauthUsers
     */
    protected $_users;
    
    /**
     * People
     * 
     * @var AnDomainEntitysetDefault
     */
    protected $_people;
    
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
            'users'           => array(
                 'limit'  => 20,
                 'offset' => 0,
                 'name'   => null      
             ),
            'name'            => null,
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
     * Return an array of connections
     * 
     * @param array $config Users configuration
     * 
     * @return array
     */
    public function getUsers($config)
    {
        $this->_load();
        $config = new KConfig($config);
        $users  = $this->_users;

        if ( $config->name )
        {
            $name = $config->name;
            foreach($users as $i => $user)
            {
                if ( strpos(strtolower($user['name']), $name) === false ) {
                    unset($users[$i]);
                }
            }
        }
        
        $users =
            array_slice($users, $config->get('offset', 0), $config->get('limit', 20));;        
                
        return $users;
    }
    
    /**
     * Set of people who are friends with the inviter
     * 
     * @return AnDomainEntiyset
     */
    public function getPeople()
    {
        $this->_load();
        return $this->_people;    
    }
    
    /**
     * Return the facebook APP ID
     * 
     * @return int
     */
    public function getAppId()
    {
        $cache = JFactory::getCache((string) $this->getIdentifier());
        $cache->setLifeTime(5*100);
        $data = $cache->get(function($session) {
            $info = $session->api->get('/app');
            return $info;
        }, array($this->_oauth_session) , '/app'.md5($this->_oauth_session->id));
        return $data['id'];        
    }
    
    /**
     * Loads the data
     * 
     * @return void
     */
    protected function _load()
    {
        if ( !isset($this->_users) )
        {
            $cache = JFactory::getCache((string) $this->getIdentifier(), '');
            $key   = 'connections_'.$this->_oauth_session->id;
            $data  = $cache->get($key);
            $service_name = $this->_service_name;
            $service      = $this->getService();
            $get_people_query = function($ids) use($service_name, $service) {
                return $service->get('repos://site/people')
                ->getQuery(true)
                ->select('@col(sessions.profileId)')
                ->where(array(
                        'sessions.profileId'=> $ids,
                        'sessions.api'      => $service_name))
                        ;
            };
            if ( !$data )
            {
                $data  = $this->_oauth_session->getApi()->get('/me/friends');
                $data  = KConfig::unbox($data);
                $data['ids'] = array_map(function($user){
                    return $user['id'];
                }, $data['data']);
                $profile_ids  = $get_people_query($data['ids'])->fetchValues('sessions.profileId');
                $data['data'] = array_filter($data['data'], function($user)
                        use ($profile_ids) {
                    return !in_array($user['id'], $profile_ids);
                });
                $cache->store($data, $key);
            }
            
            $this->_people = $get_people_query($data['ids']);
            $this->_users  = $data['data'];            
        }
    }
}