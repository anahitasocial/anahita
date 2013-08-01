<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Component Query Class
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Query
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsDomainQueryComponent extends AnDomainQuery
{
	/**
	 * Adds a condition for list of components that are assigned to actor
	 * 
	 * @param ComActorsDomainEntityActor $actor
	 * 
	 * @return ComComponentsDomainQueryComponent
	 */
	public function assignedToActor($actor)
	{
		$actortype = $actor->getEntityDescription()->getInheritanceColumnValue()->getIdentifier();
		$this
			 //either assigned to the actor
			 ->where('assignments.actor','=',$actor)
			 //or assiged to its type with global or optional		
			 ->where('(@col(assignments.actortype) = @quote('.$actortype.') AND @col(assignments.access IN (2,1) ))', 'OR')
		;
		return $this;
	}
}

