<?php

/**
 * Modifying the node behavior. After deleting bunch of nodes we want to
 * set the todo open_status_change_by to null.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTodosDomainBehaviorNode extends LibBaseDomainBehaviorNode
{
    /**
     * (non-PHPdoc).
     *
     * @see LibBaseDomainBehaviorNode::_afterRepositoryDeletenodes()
     */
    protected function _afterRepositoryDeletenodes(KCommandContext $context)
    {
        parent::_afterRepositoryDeletenodes($context);

        $this->_getRepositoryForTable('todos_todos')
             ->update(array('open_status_change_by' => null),
                      array('node_id' => $context['node_ids']));
    }
}
