<?php

define('PROCESSOR_PATH', ANPATH_BASE.'/components/com_notifications/process.php');

/**
 * Notification Repository.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsDomainRepositoryNotification extends AnDomainRepositoryDefault
{
    /**
     * If set the true it wil try to send the notification righ
     * after it has been created through a background process.
     * This require the PHP environment to have access to the shell.
     *
     * @see exec_in_background()
     *
     * @var bool
     */
    protected $_send_after_insert;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_send_after_insert = $config->send_after_insert;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'send_after_insert' => !get_config_value('notifications.use_cron', false),
        ));

        parent::_initialize($config);
    }

    /**
     * After Insert command. Called after a notification is inserted. This method
     * tries to send the notification.
     *
     * @param AnCommandContext $context The command context
     */
    protected function _afterEntityInsert(AnCommandContext $context)
    {
        parent::_afterEntityInsert($context);

        if ($this->_send_after_insert) {
            //run no more than 60 seconds
            $command = sprintf('php -d max_execution_time=60 %s/index.php %s id=%d >/dev/null 2>&1', ANPATH_BASE, PROCESSOR_PATH, $context->entity->id);
            // $command = sprintf('php -d max_execution_time=60 %s/index.php %s id=%d', ANPATH_BASE, PROCESSOR_PATH, $context->entity->id);
            // error_log($command);
            exec($command);
            // error_log(print_r($result, true));
        }
    }
}
