<?php

/**
 * Privatable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComActorsControllerBehaviorPrivatable extends ComBaseControllerBehaviorPrivatable
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        
        $this->registerCallback(
                array('after.getprivacy', 'after.setprivacy'),
                array($this, 'fetchPrivacy')
        );
    }
    
    protected function _actionGetprivacy(AnCommandContext $context)
    {        
        return;
    }
    
    /**
     * Overwrite the setPrivacy action in privatable behavior.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @see   ComActorsDomainBehaviorPrivatable
     */
    protected function _actionSetprivacy(AnCommandContext $context)
    {
        parent::_actionSetPrivacy($context);

        $data = $context->data;

        if ($data->access != 'followers') {
            $data->allowFollowRequest = false;
        }

        $this->getItem()->allowFollowRequest = (bool) $data->allowFollowRequest;
    }
    
    public function fetchPrivacy(AnCommandContext $context)
    {        
        $data = array(
            'allowFollowRequest' => (bool) $this->getItem()->allowFollowRequest,
            'access' => $this->getItem()->access,
        );
        
        if ($this->getItem()->isAdministrable()) {
            $data['leadable:add'] = $this->getItem()->getPermission(
                    'leadable:add', 
                    LibBaseDomainBehaviorPrivatable::FOLLOWER
                );
        }
        
        $content = json_encode($data);
        
        $context->response->setContent($content);
    }
    
    public function canGetprivacy()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
    
    public function canSetprivacy()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}