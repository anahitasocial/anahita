<?php 
 
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */


/**
 * An aggregate of oauth users
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectOauthUsers extends KObject implements Iterator
{
    /**
     * A connection callback to get a list of connectsion
     * 
     * @var callback
     */
    protected $_connections_callback;
    
    /**
     * A mapper callback to map the attributes of the data received into
     * the oauth user
     *
     * @var callback
     */
    protected $_mapper_callback;    
    
    /**
     * An array of users
     * 
     * @var array
     */
    protected $_users;
    
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
        
        $this->_connections_callback = KConfig::unbox($config->connections_callback);        
        $this->_mapper_callback      = KConfig::unbox($config->mapper_callback);
        
        $this->_loadUsers();
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
            'connections_callback' => function()  {return array();},
            'mapper_callback'      => function($user) {return $user;} 
        ));
        parent::_initialize($config);
    } 
    
    /**
     * Filters the user by a key
     * 
     * @param string $key
     * @param string $value
     * 
     * @return ComConnectOauthUsers;
     */
    public function filter($key, $value)
    {
        $users = clone $this;
        if ( !empty($value) )
        {
            foreach($this as  $user)
            {
                if ( strpos(strtolower($user->$key), $value) === false ) {
                    $users->extract($user);
                }
            }            
        }               
        return $users;       
    }
    
    /**
     * Applyes a limit and offset
     * 
     * @param number $offset
     * @param number $limit
     * 
     * @return ComConnectOauthUsers
     */
    public function limit($offset = 0, $limit = 20)
    {
        $users = $this->toArray();        
        $data  = array_slice($users, $offset, $limit);
        $users = $this->getService($this->getIdentifier());
        foreach($data as $user) {
            $users->insert($user);
        }
        return $users;
    }
    
    /**
     * Rewind the Iterator to the first element
     *
     * @return  void
     */
    public function rewind()
    {
        reset($this->_users);
    }

    /**
     * Checks if current position is valid
     *
     * @return  boolean
     */
    public function valid()
    {
        return !is_null(key($this->_users));
    }

    /**
     * Return the key of the current element
     *
     * @return  mixed
     */
    public function key()
    {
        return key($this->_users);
    }

    /**
     * Return the current element
     *
     * @return  mixed
     */
    public function current()
    {
        return current($this->_users);
    }

    /**
     * Move forward to next element
     *
     * @return  void
     */
    public function next()
    {
        return next($this->_users);
    } 

    /**
     * Loads the users and caches the data
     * 
     * @return void
     */
    protected function _loadUsers()
    {
        $cache = JFactory::getCache((string) $this->getIdentifier(), '');
        $key   = 'connections_';
        $data  = $cache->get($key);
        if ( !$data )
        {
            $data    = invoke_callback(array('static::DD'));
            //$this->_connections_callback
            die;
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
}