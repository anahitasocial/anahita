<?php

/**
 * Abstract Base Router.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class ComBaseRouterAbstract extends KObject implements KServiceInstantiatable
{
    /**
     * Route patterns.
     *
     * @var array
     */
    protected $_patterns;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_patterns = $config['patterns'];
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
        $package = $this->getIdentifier()->package;

        $config->append(array(
            'patterns' => array(
                '' => array('view' => $package),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton.
     *
     * @param   object  An optional KConfig object with configuration options
     * @param   object  A KServiceInterface object
     *
     * @return KDispatcherDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier)) {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Build the route.
     *
     * @param   array   An array of URL arguments
     *
     * @return array The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        $segments = array();

        if (isset($query['view'])) {
            $id = null;

            //if there's an id pluralize the view
            if (isset($query['id']) && !is_array($query['id'])) {
                //remove the singularize view
                $query['view'] = AnInflector::pluralize($query['view']);
                $id = $query['id'];
                unset($query['id']);
            }

            //prevent duplicate name
            if ($query['view'] != $this->getIdentifier()->package) {
                $segments[] = $query['view'];
            }

            unset($query['view']);

            if ($id) {
                $segments[] = $id;
            }
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array   The segments of the URL to parse.
     *
     * @return array The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = array();

        if (empty($segments)) {
            $vars['view'] = $this->getIdentifier()->package;
        } elseif (count($segments) == 1) {
            if (is_numeric(current($segments))) {
                $vars['view'] = AnInflector::singularize($this->getIdentifier()->package);
                $vars['id'] = array_pop($segments);
            } else {
                $vars['view'] = array_pop($segments);
            }
        } else {
            $path = implode('/', $segments);
            $matches = array();

            if (preg_match('/(\w+\/)?(\d+)(\/\w+)?/', $path, $matches)) {
                $view = (!empty($matches[1])) ? trim($matches[1], '/') : $this->getIdentifier()->package;
                $vars['view'] = AnInflector::singularize($view);
                $vars['id'] = $matches[2];

                if (isset($matches[3])) {
                    $vars['get'] = trim($matches[3], '/');
                }

                $segments = array_filter(explode('/', str_replace($matches[0], '', $path)));
            }
        }

        return $vars;
    }
}
