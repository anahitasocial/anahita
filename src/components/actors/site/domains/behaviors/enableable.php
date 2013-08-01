<?php

/** 
 * LICENSE: ##LICENSE##
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
 * Enableable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorEnableable extends LibBaseDomainBehaviorEnableable
{
	/**
	 * {@inheritdoc}
	 * 
	 * Only brings the actor that are enabled
	 *
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void
	 */
	protected function _beforeRepositoryFetch(KCommandContext $context)
	{
        if ( get_viewer()->admin() )
            return;
        
		$query = $context->query;
		$repos = $query->getRepository();
        if ( get_viewer()->isAdministrator() )
        {
            $ids = get_viewer()->administratingIds->toArray();    
        }
		$ids[] = 0;
		$ids   = implode(',', $ids);
		$query->where("IF(@col(enabled) = FALSE, @col(id) IN ($ids) ,1)");
	}
}