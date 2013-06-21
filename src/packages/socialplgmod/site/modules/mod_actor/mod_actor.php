<?php defined('KOOWA') or die('Restricted access');

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Actor
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

$actor  = KService::get('repos:actors.actor')->getQuery()->disableChain()->fetch( $params->get('actor_id', 0));

if ( $actor )
{
    $viewer = get_viewer();
    
    if($params->get('use_actor_name', 1))
        $module->title  = KService::get('com://site/actors.template.helper')->name($actor);
    
    $followers = $actor->followers->limit($params->get('num_of_follwers_in_grid', 15));
    
    if($params->get('only_followers_with_avatars', 1))
    	$followers->where('filename', '!=', '');
    
    print KService::get('mod://site/actor.html', array(
            'params'=>$params
    ))
    ->actor($actor)
    ->followers($followers);
}

