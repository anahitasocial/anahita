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
 * {@inheritdoc}
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorPrivatable extends LibBaseDomainBehaviorPrivatable
{    
    /**
     * {@inheritdoc}
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {        
        if ( KService::has('com:people.viewer') && 
        			is_person(get_viewer()) && get_viewer()->admin() )
            return;
                     
        $query		= $context->query;
        $repository = $query->getRepository();
        $config 	= pick($query->privacy, new KConfig());
           
        $config->append(array(
            'visible_to_leaders'  => true,
            'viewer'              => get_viewer(),
            'graph_check'		  => true
        ));
      
        $where = $this->buildCondition('@col(id)', $config, '@col(access)');
                                
        $query->where($where);
    }    
}