<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Represents a vote node on voted object
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainEntityVotedown extends ComBaseDomainEntityEdge
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
            'aliases'  => array(
                    'voter'   => 'nodeA',
                    'votee'   => 'nodeB'
            )
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Resets the votable stats
     *
	 * KCommandContext $context Context
	 * 
     * @return void
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {                
        $this->votee->getRepository()->getBehavior('votable')->resetStats(array($this->votee));
    }
    
    /**
     * Resets the votable stats
     *
     * @return void
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {
        $this->votee->getRepository()->getBehavior('votable')->resetStats(array($this->votee));
    }	
}