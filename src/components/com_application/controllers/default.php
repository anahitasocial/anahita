<?php

/**
 *
 * @category   Anahita
 * @package    com_application
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2017 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       https://www.Anahita.io
 */
class ComApplicationControllerDefault extends LibBaseControllerResource implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Renders the page.
     *
     * @param AnCommandContext $context
     */
    protected function _actionRender(AnCommandContext $context)
    {
        if ($context->data instanceof Exception) {
            $this->getView()->content($context->data);
        } else {
            $this->getView()->content($context->response->getContent());
        }

        $content = $this->getView()->display();

        $context->response->setContent($content);
        $context->response->setContentType($this->getView()->mimetype);
    }
}
