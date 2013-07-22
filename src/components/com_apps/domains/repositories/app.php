<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Repository
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * App repository
 *
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Repository
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsDomainRepositoryApp extends AnDomainRepositoryDefault
{
	/**
	 * Before Fetch
	 *
	 * @param KCommandContext $context
	 */
	protected function _beforeRepositoryFetch(KCommandContext $context)
	{
	    
		$query = $context->query;
		//only bring the actor apps
		if ( $query->actor ) 
		{
			$actor      = $query->actor;
			$identifier = $actor->description()->getInheritanceColumnValue()->getIdentifier();
			$query->component($actor->component,'<>');
			$always = $this->getService('repos:apps.assignment')
				->getQuery()->access(1)
				->columns('app.id')
				->where('@col(actortype.name) = "'.$identifier.'"')
				;
			$never = $this->getService('repos:apps.assignment')
				->getQuery()->access(2)
				->columns('app.id')
				->where('@col(actortype.name) = "'.$identifier.'"')
				;
			$installed = $this->getService('repos:apps.enable')
							->getQuery()->actor($actor)->columns('app.id')
				;			
			
			if ( $query->access === ComAppsDomainEntityApp::ACCESS_OPTIONAL )
			{
			    //bring all except never
			    $query->where("(@col(always) = 1 OR @col(id) NOT IN ($never))");
			}				
			else 
			{
			    //bring installed or always
			    $query->where("IF(@col(id) IN ($installed), @col(always) = 1 OR @col(id) NOT IN ($never), @col(always) = 1 OR @col(id) IN ($always))");
			}
		}
	}
}