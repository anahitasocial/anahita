<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainEntityComponent extends AnDomainEntityDefault implements KEventSubscriberInterface
{
    /**
     * Subscriptions.
     *
     * @var array
     */
    private $__subscriptions = array();

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $this->getService('anahita:language')->load('com_'.$this->getIdentifier()->package);

        $config->append(array(
            'resources' => array('components'),
            'attributes' => array(
                'meta' => array(
                    'required' => false,
                    'default' => ''
                ),
                'enabled',
            ),
            'behaviors' => array(
                'orderable',
                'authorizer',
            ),
            'query_options' => array(
                'where' => array('parent' => 0)
            ),
            'aliases' => array(
                'component' => 'option',
             ),
            'auto_generate' => true,
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @see AnDomainEntityAbstract::__get()
     */
    public function __get($key)
    {
        if ($key == 'name') {
            return ucfirst(str_replace('com_', '', $this->option));
        }

        return parent::__get($key);
    }

    /**
     * Registers event dispatcher.
     *
     * @param KEventDispatcher $dispatcher Event dispatche
     */
    public function registerEventDispatcher(KEventDispatcher $dispatcher)
    {
        $dispatcher->addEventSubscriber($this);
    }

    /**
     * Get the priority of the handler.
     *
     * @return int The event priority
     */
    public function getPriority()
    {
        return $this->ordering;
    }

    /**
     * Get a list of subscribed events.
     *
     * Event handlers always start with 'on' and need to be public methods
     *
     * @return array An array of public methods
     */
    public function getSubscriptions()
    {
        if (!$this->__subscriptions) {
            $subscriptions = array();

            //Get all the public methods
            //$reflection = new ReflectionClass($this);
            //foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
            foreach ($this->getMethods() as $method) {
                if (substr($method, 0, 2) == 'on') {
                    $subscriptions[] = $method;
                }
            }

            $this->__subscriptions = $subscriptions;
        }

        return $this->__subscriptions;
    }

    /**
     * Return an array of identifiers within the component.
     *
     * @param string $class The class from which the entities are inherting
     *
     * @return array()
     */
    public function getEntityRepositories($class)
    {
        $identifiers = $this->getEntityIdentifiers($class);

        foreach ($identifiers as $i => $identifier) {
            $identifiers[$i] = AnDomain::getRepository($identifier);
        }

        return $identifiers;
    }

    /**
     * Return an array of identifiers within the component.
     *
     * @param string $class The class from which the entities are inherting
     *
     * @return array()
     */
    public function getEntityIdentifiers($class)
    {
        $registry = $this->getService('application.registry', array('key' => $this->getIdentifier()));

        if (!$registry->offsetExists($class.'-identifiers')) {
            $path = ANPATH_ROOT.DS.'components'.DS.'com_'.$this->getIdentifier()->package.DS.'domains'.DS.'entities';

            $identifiers = array();

            if (file_exists($path)) {

                //scandir without . and .. hiident directories
                $files = array_values(preg_grep('/^([^.])/', scandir($path)));

                foreach ($files as $file) {
                    $name = explode('.', basename($file));
                    $name = $name[0];
                    $identifier = clone $this->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;

                    try {
                        if (is($identifier->classname, $class)) {
                            $identifiers[] = $identifier;
                        }
                    } catch (Exception $e) {
                    }
                }
            }

            $registry->offsetSet($class.'-identifiers', $identifiers);
        }

        $identifiers = $registry->offsetGet($class.'-identifiers');

        return $identifiers;
    }
}
