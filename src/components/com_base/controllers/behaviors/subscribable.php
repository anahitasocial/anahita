<?php

/**
 * Subscribable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerBehaviorSubscribable extends AnControllerBehaviorAbstract
{
    /**
     * If the viewer is subscribe then unsubscribe, if not subscribe then subscribe.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionTogglesubscription($context)
    {
        if ($this->getItem()->subscribed(get_viewer())) {
            $ret = $this->_mixer->execute('unsubscribe', $context);
        } else {
            $ret = $this->_mixer->execute('subscribe', $context);
        }

        return $ret;
    }

    /**
     * Subscribe the viewer to the subscribable object.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionSubscribe($context)
    {
        $this->getItem()->addSubscriber(get_viewer());
    }

    /**
     * Remove the viewer's subscription from the subscribable object.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionUnsubscribe($context)
    {
        $this->getItem()->removeSubscriber(get_viewer());
    }
}
