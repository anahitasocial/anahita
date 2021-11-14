<?php

/**
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */

/**
 * Pinnable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerBehaviorPinnable extends AnControllerBehaviorAbstract
{
    /**
     * Pin Entity.
     *
     * @param AnCommandContext $context
     */
    protected function _actionPin($context)
    {
        $context->response->status = AnHttpResponse::RESET_CONTENT;
        $this->getItem()->pinned = 1;

        return $this->getItem();
    }

    /**
     * Unpin Entity.
     *
     * @param AnCommandContext $context
     */
    protected function _actionUnpin($context)
    {
        $context->response->status = AnHttpResponse::RESET_CONTENT;
        $this->getItem()->pinned = 0;

        return $this->getItem();
    }
}
