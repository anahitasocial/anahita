<?php 

/**
 * Clear all the unsent notifications (sets them all to sent)
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotificationsControllerClear extends ComBaseControllerResource
{        
    private $_notifications = null;
    
    private $_count = 0;
    
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback(array(
            'before.get', 
            'before.post',
        ), array($this, 'fetchNotifications'));
        
        $this->registerCallback(array(
            'after.get', 
            'after.post',
        ), array($this, 'createResponse'));
    }
    
    protected function _actionGet(AnCommandContext $context)
    {
        $this->_count = count($this->_notifications);
    }
    
    protected function _actionPost(AnCommandContext $context)
    {
        $this->_count = count($this->_notifications);
        
        foreach($this->_notifications as $notification) {
            $notification->status = ComNotificationsDomainEntityNotification::STATUS_SENT;
        }
    }
    
    public function fetchNotifications(AnCommandContext $context)
    {
        $query = $this->getService('repos:notifications.notification')
                      ->getQuery(true)
                      ->status(ComNotificationsDomainEntityNotification::STATUS_NOT_SENT);
                      
        $this->_notifications = $query->fetchSet();              
    }
    
    public function createResponse(AnCommandContext $context)
    {
        $content = $this->getView()
        ->set('count', $this->_count)
        ->display(); 
        
        $context->response->setContent($content);
    }
    
    public function canGet()
    {
        return get_viewer()->admin();
    }
    
    public function canPost()
    {
        return get_viewer()->admin();
    }
}