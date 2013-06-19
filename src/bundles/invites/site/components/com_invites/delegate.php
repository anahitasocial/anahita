<?php
/**
 * @version		$Id
 * @category	Anahita_Invites
 * @package		Site
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Connect App
 * 
 * @category	Com_Invites
 */
class ComInvitesDelegate extends ComAppsDomainDelegateDefault
{
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {      
        $config->append(array(
            'default_assignments' => array('person'=>self::ASSIGNMENT_ALWAYS,'group'=>self::ASSIGNMENT_NEVER),
            'features' => false
        ));
        
        parent::_initialize($config);
    }
}