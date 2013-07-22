<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * An administrator actor, is an actor that can administrate other actors. Person a
 * actor is a prominent example of an administrator actor 
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorAdministrator extends AnDomainBehaviorAbstract
{
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
            'attributes'    => array(
                'administratingIds' => array('type'=>'set', 'default'=>'set')
            )
        ));
                
        parent::_initialize($config);
    }
    
    /**
     * Return if the receiver actor acts as the administrator of the 
     * $actor
     * 
     * @param ComActorsDomainEntityActor $actor Actor Entity
     * 
     * @return boolean
     */
    public function administrator($actor)
    {
        if ( $this->_mixer->eql($actor) )
            return true;
            
        return $this->administratingIds->offsetExists($actor->id);
    }  
}