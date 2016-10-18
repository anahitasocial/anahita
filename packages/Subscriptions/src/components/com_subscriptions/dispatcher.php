<?php

require_once ANPATH_LIBRARIES.'/merchant/merchant.php';

/**
 * Subscription Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDispatcher extends ComBaseDispatcherDefault
{
    /**
     * (non-PHPdoc).
     *
     * @see ComBaseDispatcher::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        if ($this->action === 'confirm' && $this->token) {
            $context->data->append(array(
               '_action' => 'confirm',
               'token' => $this->token,
            ));

            return $this->execute('post', $context);
        }

        return parent::_actionDispatch($context);
    }

    /**
     * Redirects to HTTPs.
     *
     * @param KCommandContext $context
     */
    public function redirectHttps(KCommandContext $context)
    {
        if (KRequest::url()->scheme === 'http') {
            $url = clone KRequest::url();
            $url->scheme = 'https';
            $context->response->setRedirect($url);
            return false;
        }
    }
}
