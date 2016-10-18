<?php

/**
 * People Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDispatcher extends ComBaseDispatcherDefault
{
    /**
     * Handles passowrd token before dispatching.
     *
     * (non-PHPdoc)
     *
     * @see ComBaseDispatcherDefault::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        if (
            $this->getController()->getIdentifier()->name === 'session' &&
            $this->token != ''
        ) {
            $this->getController()->execute('tokenlogin', $context);
            $context->response->send();
            exit(0);
        }

        return parent::_actionDispatch($context);
    }

    /**
     * If session throws LibBaseControllerExceptionUnauthorized exception
     * that means the user has entere wrong credentials. In that case
     * let the application handle the error.
     *
     * (non-PHPdoc)
     *
     * @see ComBaseDispatcherDefault::_actionException()
     */
    protected function _actionException(KCommandContext $context)
    {
        if (
            $context->data instanceof LibBaseControllerExceptionUnauthorized &&
            $this->getController() instanceof ComPeopleControllerSession
        ) {
            $context->response->send();
            exit(0);
        } else {
            parent::_actionException($context);
        }
    }
}
