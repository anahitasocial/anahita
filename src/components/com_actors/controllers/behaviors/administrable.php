<?php

/**
 * Administrable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerBehaviorAdministrable extends AnControllerBehaviorAbstract
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

        $this->registerCallback(
                array('before.addAdmin', 'before.removeAdmin'),
                array($this, 'fetchAdmin')
        );
    }

    /**
     * Remove admin.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionRemoveAdmin(AnCommandContext $context)
    {
        $this->getItem()->removeAdministrator($this->admin);
    }

    /**
     * Add Admin.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionAddAdmin(AnCommandContext $context)
    {
        $this->getItem()->addAdministrator($this->admin);
    }

    /**
     * Get Candidates.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionGetCandidates(AnCommandContext $context)
    {
        if ($context->request->getFormat() != 'html') {
            $data = $context->data;
            $canditates = $this->getItem()->getAdminCanditates();
            $canditates->keyword($this->value)->limit(10);
            $people = array();

            foreach ($canditates as $person) {
                $people[] = array(
                  'id' => $person->id,
                  'value' => $person->name, );
            }

            $this->getView()->set($people);

            return $people;
        }
    }

    /**
     * Get settings.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionGetSettings(AnCommandContext $context)
    {
        $entity = $this->getItem();
        $this->getToolbar('actorbar')->setActor($entity);
        $this->getToolbar('actorbar')
             ->setTitle(
                 sprintf(AnTranslator::_('COM-ACTORS-PROFILE-HEADER-EDIT'),
                 $entity->name));
                 
        $dispatcher = $this->getService('anahita:event.dispatcher');
        $entity->components->registerEventDispatcher($dispatcher);
        $dispatcher->addEventListener('onSettingDisplay', $this->_mixer);
    }
    
    protected function _actionGetPrivacy(AnCommandContext $context)
    {        
        $data = array(
            'allowFollowRequest' => (bool) $this->getItem()->allowFollowRequest,
            'access' => $this->getItem()->access,
            'permissions' => array(
                'leadable:add' => $this->getItem()
                    ->getPermission(
                        'leadable:add', 
                        LibBaseDomainBehaviorPrivatable::FOLLOWER
                    ),
            ),
        );
        
        $content = $this->getView()
        ->set('data', $data)
        ->layout('privacy')
        ->display();
        
        $context->response->setContent($content);
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
            $this->getState()
                 ->requester = $this->getItem()
                                    ->requesters
                                    ->fetch($data->requester);
        }
    }

    /**
     * Fetches the requester.
     *
     * @param AnCommandContext $context Context parameter
     */
    public function fetchAdmin(AnCommandContext $context)
    {
        $data = $context->data;

        if ($this->getItem()) {
            $this->getState()
                 ->admin = $this->getService('repos:people.person')
                                ->fetch($data->adminid);
        }
    }
    
    public function canGetprivacy()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}
