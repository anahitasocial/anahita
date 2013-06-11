<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Identifiable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorIdentifiable extends LibBaseControllerBehaviorIdentifiable
{	    	
    /**
     * Fetches an entity
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract The identified entity
     */
    public function fetchEntity(KCommandContext $context)
    {
    	$entity = parent::fetchEntity($context);
    	
    	//set the entity owner as the context actor of the controller
    	if ( $entity && $this->getRepository()->isOwnable() && $this->isOwnable() ) {
            $this->setActor($entity->owner);
    	}
    	
    	return $entity;
    }	
}