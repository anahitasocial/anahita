<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Tags
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Tag association   
 * 
 * @category   Anahita
 * @package    Com_Tags
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTagsDomainEntityAssociation extends ComBaseDomainEntityEdge
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
            'aliases' => array(
                'taggable' => 'nodeA',
                'tag'      => 'nodeB'  
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
        $this->taggable->getRepository()->getBehavior('taggable')->resetStats(array($this->taggable));
    }
    
    /**
     * Resets the votable stats
     *
     * KCommandContext $context Context
     * 
     * @return void
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {       
        $this->taggable->getRepository()->getBehavior('taggable')->resetStats(array($this->taggable));
    }        
}