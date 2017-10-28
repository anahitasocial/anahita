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
 * @link       https://www.GetAnahita.com
 */
class ComApplicationControllerDefault extends LibBaseControllerResource implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'theme' => 'base'
        ));

        $config->append(array(
            'view' => 'tmpl:'.$config->theme.'.view.'.$this->getIdentifier()->name.'.'.$config->request->getFormat(),
        ));

        parent::_initialize($config);
    }

    /**
     * Renders the page.
     *
     * @param KCommandContext $context
     */
    protected function _actionRender(KCommandContext $context)
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
