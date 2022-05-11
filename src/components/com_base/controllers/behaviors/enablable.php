<?php

/**
 * Enablable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerBehaviorEnablable extends AnControllerBehaviorAbstract
{
    /**
     * Enable Entity.
     *
     * @param AnCommandContext $context
     */
    protected function _actionEnable($context)
    {
        $context->response->status = AnHttpResponse::RESET_CONTENT;
        $this->getItem()->enable();

        return;
    }

    /**
     * Disable Entity.
     *
     * @param AnCommandContext $context
     */
    protected function _actionDisable($context)
    {
        $context->response->status = AnHttpResponse::RESET_CONTENT;
        $this->getItem()->disable();

        return;
    }

    /**
     * Authorize enable.
     *
     * @return bool
     */
    public function canEnable()
    {
        return $this->canEdit();
    }

    /**
     * Authorize disable.
     *
     * @return bool
     */
    public function canDisable()
    {
        return $this->canEdit();
    }
}
