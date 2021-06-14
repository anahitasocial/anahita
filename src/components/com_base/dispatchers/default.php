<?php

/**
 * Default Base Dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseDispatcherDefault extends LibBaseDispatcherComponent
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        parent::_initialize($config);

        if ($config->request->view) {
            $config->controller = $config->request->view;
        }
    }

    /**
     * Allows the component to handle exception. By default this
     * action passes the exception to the application exception handler.
     *
     * @param AnCommandContext $context Command context
     */
    protected function _actionException(AnCommandContext $context)
    {
        $viewer = $this->getService('com:people.viewer');

        if ($viewer->guest() && $context->data instanceof LibBaseControllerExceptionUnauthorized) {
            throw new AnErrorException(array('You must login first to see this resource!'), AnHttpResponse::UNAUTHORIZED);
            return;
        } else {
            $this->getService('application.dispatcher')->execute('exception', $context);
        }
    }
}
