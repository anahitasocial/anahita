<?php

define('LOG_LEVEL_DEBUG', 'DEBUG');
define('LOG_LEVEL_INFO',  'INFO');
define('LOG_LEVEL_WARN',  'WARN');
define('LOG_LEVEL_ERROR', 'ERROR');

/**
 * Loggable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 *
 * @uses AnLog
 */
class LibBaseControllerBehaviorLoggable extends AnControllerBehaviorAbstract
{
    /**
     * Log instance.
     *
     * @var AnLog
     */
    protected $_log;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_log = $config->log;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $log = $this->getService('anahita:log', array('file' => 'system_log.php'));
        $config->append(array(
            'log' => $log,
        ));

        parent::_initialize($config);
    }

    /**
     * Logs an entry.
     *
     * @param array|string $entry
     *
     * @see AnLog::addEntry options
     *
     * @return mixed
     */
    public function log($entry, $level = LOG_LEVEL_INFO)
    {
        if (!is_array($entry)) {
            $entry = array('comment' => $entry, 'level' => $level);
        }

        $this->_log->addEntry($entry);

        return $this;
    }
}
