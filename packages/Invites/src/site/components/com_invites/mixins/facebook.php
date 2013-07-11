<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Mixins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Set of people who are an actor fb friends
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Mixins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesMixinFacebook extends KMixinAbstract
{   
    /**
     * Return the APPID
     * 
     * @return int
     */ 
    public function getAppID()
    {        
        $key   = md5($this->_mixer->getToken());
        $cache = JFactory::getCache((string) 'ComInvitesMixinFacebook');
        $cache->setLifeTime(5*1000);
        $data = $cache->get(function($session) {
            $info = $session->get('/app');
            return $info;
        }, array($this->_mixer) , '/app'.$key);
        return $data['id'];        
    }
    
    /**
     * Return a set of people who are fb friends
     * 
     * @return set
     */
    public function getConnections()
    {
        $cache = JFactory::getCache((string) 'ComInvitesMixinFacebook', '');
        $key   = md5($this->_mixer->getToken());
        $data  = $cache->get($key);
        if ( !$data  )
        {
            $data   = $this->_mixer->get('/me/friends');
            $data   = KConfig::unbox($data);
            $data   = array_map(function($user) {return $user['id'];}, $data['data']);
            $cache->store($data, $key);
        }    
        
        $query = $this->getService('repos://site/people')->getQuery(true)
        ->where(array(
                'sessions.profileId'=> $data,
                'sessions.api'      => 'facebook'));
        return $query->toEntitySet();       
    } 
}