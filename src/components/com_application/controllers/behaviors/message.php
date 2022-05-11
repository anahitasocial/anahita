<?php

/**
 * Message Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.Anahita.io
 */
class ComApplicationControllerBehaviorMessage extends AnControllerBehaviorAbstract
{
    /**
     * Sets a message.
     *
     * @param string $type    The message type
     * @param string $message The message text
     * @param bool   $global  A flag to whether store the message in the global queue or not
     */
    public function setMessage($message, $type = 'info', $global = false)
    {
        //if ajax send back the message
        //in the header
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()
            ->setHeader('X-Message', json_encode(array(
                'text' => AnTranslator::_($message),
                'type' => $type,
                'global' => $global,
            )));
        }
    }
}
