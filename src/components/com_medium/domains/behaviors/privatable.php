<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Medium
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
 * @package    Com_Medium
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumDomainBehaviorPrivatable extends LibBaseDomainBehaviorPrivatable
{    
    /**
     * {@inheritdoc}
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        if ( KService::has('com:people.viewer') && is_person(get_viewer()) && get_viewer()->admin() )
            return;
                    
        $query		= $context->query;
        $repository = $query->getRepository();
        $config 	= pick($query->privacy, new KConfig());
           
        $config->append(array(
            'viewer'             => get_viewer(),                
            'graph_check'		 => true
        ));
    
        if ( $repository->hasBehavior('ownable')  )
        {
            //do a left join operation just in case an owner is missing
            $query->link('owner',array('type'=>'weak','bind_type'=>false));
            $config->append(array(
               'use_access_column' => '@col(access)'    
            ));
            $c1 = $this->_createWhere('@col(owner.id)', $config, '@col(owner.access)');
            $c2 = $this->_createWhere('@col(owner.id)', $config, $config->use_access_column);
            $where = "IF($c1, $c2, 0)";
            $query->where($where);
        }
    }    
}