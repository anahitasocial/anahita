<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * story Permission. 
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesControllerPermissionStory extends LibBaseControllerPermissionDefault
{
    /**
     * Can't add a story if the story controller is dispatched.
     *
     * @return bool
     */
    public function canAdd()
    {
        return !$this->_mixer->isDispatched();
    }

    /**
     * Checks if _actionBrowse.
     *
     * @return bool
     */
    public function canBrowse()
    {
        if (!$this->actor) {
            return false;
        }
    }
}
