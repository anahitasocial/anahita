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
class ComConnectOauthUsers extends KObjectSet
{
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
}