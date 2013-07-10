<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invite
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Invite JSON view
 *
 * @category   Anahita
 * @package    Com_Invite
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.Json>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesViewFacebookJson extends ComBaseViewJson
{       
    /**
     * (non-PHPdoc)
     * @see LibBaseViewJson::_getItem()
     */
    protected function _getItem()
    {
        $data['data'] = $this->_state->social_inviter->getUsers();
        $data['pagination'] = array(
                    'limit'  => (int)$this->_state->get('limit'),
                    'offset' => (int)$this->_state->get('start'));
        return $data;  
    }    
}