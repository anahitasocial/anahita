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
 * Set of people who are an actor fb friends
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesDomainEntitysetFbfriend extends KObject implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if ( !$config->actor ) {
            throw new InvalidArgumentException(":actor option is missing");
        }
        
        $actor    = $config->actor;
        $container->get('repos://site/connect.session');
        $facebook = $actor->sessions->facebook;
        if ( $facebook )
        {
            $cache = JFactory::getCache((string) $config->service_identifier, '');
            $key   = 'fb_'.$actor->id;
            $data  = $cache->get($key);
            if ( !$data  )
            {
                $data   = $facebook->get('/me/friends');
                $data   = KConfig::unbox($data);
                $data   = array_map(function($user) {return $user['id'];}, $data['data']);
                $cache->store($data, $key);
            }           
        } else {
            $data = array(-1);
        }
        
        $query = $container->get('repos://site/people')->getQuery(true)
                ->where(array(
                    'sessions.profileId'=> $data,
                    'sessions.api'      => 'facebook'));

        return $query->toEntityset();        
    }  
}