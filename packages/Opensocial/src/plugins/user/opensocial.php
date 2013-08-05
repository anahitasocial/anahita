<?php 
/**
 * @version		1.0.3
 * @category	Anahita Social Engine™
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * Opensocial user plugin
 *
 * @author		Rastin Mehr  <info@rmdstudio.com>
 * @package		Joomla
 * @subpackage	Anahita
 * @since 		1.5
 */
class PlgUserOpensocial extends JPlugin 
{
    /**
     * store user method
     *
     * Method is called before user data is deleted from the database
     *
     * @param 	array		holds the user data
     */
    public function onAfterDeleteUser($user)
    {
        $actor_profile = KService::get('repos:opensocial.profile')
            ->find(array('person.userId'=>$use['id']));
        if ( $actor_profile ) {
            $actor_profile->delete()->save();
        }
    }
}