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
class ComInvitesHelperFacebook extends KObject
{
    /**
     * Retunr the facebook App ID from a session
     * 
     * @param $session Connect API
     * 
     * @return int
     */
    public function getFacebookAppId($actor)
    {
        $session = $this->getService('repos://site/connect.session')
            ->fetch(array('owner'=>$actor, 'api'=>'facebook'));
        $cache = JFactory::getCache((string) $this->getIdentifier());
        $cache->setLifeTime(5*1000);
        $data = $cache->get(function($session) {
            $info = $session->api->get('/app');
            return $info;
        }, array($session) , '/app'.md5($session->id));
        return $data['id'];
    }
    
    /**
     * Return a set of facebook firne
     * 
     * @return AnDomainEntityset
     */
    public function getFacebookFriends()
    {
        
    }
}