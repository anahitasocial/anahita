<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Modifying the node behavior. After deleting bunch of nodes we want to
 * set the todo open_status_change_by to null 
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTodosDomainBehaviorNode extends LibBaseDomainBehaviorNode
{
    /**
     * (non-PHPdoc)
     * @see LibBaseDomainBehaviorNode::_afterRepositoryDeletenodes()
     */
    protected function _afterRepositoryDeletenodes(KCommandContext $context)
    {
        parent::_afterRepositoryDeletenodes($context);
        
        $this->_getRepositoryForTable('todos_todos')
            ->update(array('open_status_change_by'=>NULL),array('node_id'=>$context['node_ids']));
    }
}