<?php

/**
 * Requestable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerBehaviorRequestable extends AnControllerBehaviorAbstract
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
                array('before.confirmRequest', 'before.ignoreRequest'),
                array($this, 'fetchRequester')
        );
    }
    
    /**
     * Gets list of follow requests
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionGetFollowRequests(AnCommandContext $context)
    {
        $data = $this->getItem()->requesters
        ->limit($this->limit, $this->start)
        ->toArray();

        $this->getView()
        ->set('data', $data)
        ->set('pagination', array(
            'offset' => $this->start,
            'limit' => $this->limit,
            'total' => count($data),
        ));
        return $data;
    }
    
    /**
     * Confirm a request.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionConfirmRequest(AnCommandContext $context)
    {
        $this->getItem()->addFollower($this->getState()->requester);
    }

    /**
     * Ignores a request.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionIgnoreRequest(AnCommandContext $context)
    {
        $this->getItem()->removeRequester($this->getState()->requester);
    }
    
    /**
     * Fetches the requester.
     *
     * @param AnCommandContext $context Context parameter
     */
    public function fetchRequester(AnCommandContext $context)
    {
        $data = $context->data;

        if ($this->getItem()) {
            $requester = $this->getItem()->requesters->fetch($data->requester);
            $this->getState()->requester = $requester;
        }
    }
}