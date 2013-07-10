<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Permission
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller_Permission
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */

class ComInvitesControllerPermissionFacebook extends LibBaseControllerPermissionAbstract
{
    /**
     * Fb Inviter 
     * 
     * @var ComInvitesSocialinviterFacebook
     */
    protected $_inviter;
        
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
    
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * Return whether we can use the facebook inviter or not
     * 
     * @return boolean
     */
    public function canInvite()
    {
         return $this->getInviter()->canInvite();   
    }
    
    /**
     * Get the inviter
     * 
     * @return ComInvitesSocialinviterFacebook
     */
    public function getInviter()
    {
        if ( !isset($this->_inviter) )
        {
            $this->_inviter =
            $this->getService('com://site/invites.socialinviter.facebook', array(
                    'inviter' => get_viewer()
            ));
            
            $this->_mixer->social_inviter = $this->_inviter;
        }
        return $this->_inviter;
    }
}