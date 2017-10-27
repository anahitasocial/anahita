<?php

/**
 * Message Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationControllerBehaviorMessage extends AnControllerBehaviorAbstract
{
    /**
     * Check if the behavior is enabled or not.
     *
     * @var bool
     */
    protected $_enabled = false;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_enabled = $config->enabled;
        $namespace = $this->_getQueueNamespace(false);

        $session = KService::get('com:sessions', array(
                        'namespace' => $namespace->namespace,
                        'storage' => (PHP_SAPI == 'cli') ? 'none' : 'database'
                    ));

        $data = array();

        if ($this->_enabled) {
            $data = (array) $session->set($namespace->queue, new stdClass(), $namespace->namespace);
        }

        $config->mixer->getState()->flash = new ComApplicationControllerBehaviorMessageFlash($data);

        static $once;

        if (! $once) {
            $_SESSION['__controller_persistance'] = array('controller.queue' => new stdClass());
            $once = true;
        }
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
            'enabled' => KRequest::format() != 'json' && $config->mixer->isDispatched(),
        ));

        parent::_initialize($config);
    }

    /**
     * If the message is still in the flash, push that to the global
     * message stack. This gives a chance for the message to be seen.
     *
     * @param KCommandContext $context
     */
    protected function _afterControllerGet(KCommandContext $context)
    {
        $flash = $this->_mixer->getState()->flash;
        $message = $flash->getMessage();

        if ($message) {
            $message['message'] = AnTranslator::_($message['message']);
            $this->storeValue('message', $message, true);
        }
    }

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
                'type' => $type
            )));
        } else {
            $this->storeValue('message', array('type' => $type, 'message' => $message), $global);
        }
    }

    /**
     * Stores a value in the session. This value is removed in the next
     * request.
     *
     * @param string $key    Key to use to store the value
     * @param string $value  The value
     * @param bool   $global Global queue flag
     */
    public function storeValue($key, $value, $global = false)
    {
        if ($this->_enabled) {
            $namespace = $this->_getQueueNamespace($global);
            $session = KService::get('com:sessions', array('namespace' => $namespace->namespace));

            $queue = $session->get($namespace->queue, new stdClass);
            $queue->$key = $value;

            if (! $global && $this->_mixer->flash) {
                $this->_mixer->flash->$key = $value;
            }

            $session->set($namespace->queue, $queue);
        }
    }

    /**
     * Retreive a stored value from the session.
     *
     * @param string $key    The value key
     * @param bool   $global Global queue flag
     */
    public function retrieveValue($key, $global = false)
    {
        $ret = null;

        if ($this->_enabled) {
            $namespace = $this->_getQueueNamespace($global);
            $session = KService::get('com:sessions', array('namespace' => $namespace->namespace));
            $queue = $session->get($namespace->queue, new stdClass());
            $ret = isset($queue[$key]) ? $queue[$key] : null;
        }

        return $ret;
    }

    /**
     * Return a value queue. If global is set then it returns the global
     * queue.
     *
     * @param bool $global
     *
     * @return array
     */
    protected function _getQueueNamespace($global = false)
    {
        if ($global) {
            $store = 'application.queue';
            $namespace = '__anahita';
        } else {
            $store = 'controller.queue';
            $namespace = 'controller_persistance';
        }

        return new KConfig(array('queue' => $store, 'namespace' => $namespace));
    }

    /**
     * Return the object handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return KMixinAbstract::getHandle();
    }
}
