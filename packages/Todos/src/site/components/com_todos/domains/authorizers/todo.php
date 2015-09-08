<?php

/**
 * Todo Authorizer.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTodosDomainAuthorizerTodo extends ComMediumDomainAuthorizerDefault
{
    /**
     * Check if a node authroize being updated.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return bool
     */
    protected function _authorizeEdit($context)
    {
        $ret = parent::_authorizeEdit($context);

        if ($ret === false) {
            if ($this->_entity->isOwnable()) {
                $action = $this->_entity->component.':'.$this->_entity->getIdentifier()->name.':add';

                if ($this->_entity->owner->authorize('action', $action)) {
                    return true;
                }
            }
        }

        return $ret;
    }
}
