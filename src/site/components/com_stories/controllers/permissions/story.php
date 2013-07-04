<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * story Permission. 
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerPermissionStory extends LibBaseControllerPermissionDefault
{    
    /**
     * Can't add a story if the story controller is dispatched
     *
     * @return boolean
     */
    public function canAdd()
    {
        return !$this->_mixer->isDispatched();
    }    
    
    /**
     * Checks if _actionBrowse
     *
     * @return boolean
     */
    public function canBrowse()
    {
        if ( !$this->actor ) {
            return false;
        }
    }    
}