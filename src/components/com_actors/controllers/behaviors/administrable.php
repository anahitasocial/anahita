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
        
        $this->registerCallback(
                array('after.addApp', 'after.removeApp'),
                array($this, 'fetchApp')
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
    
    protected function _actionGetApps(AnCommandContext $context)
    {
        $apps = array();
        $components = $this->getService('com:actors.domain.entityset.component', array(
                'actor' => $this->getItem(),
                'can_enable' => true,
            ));
            
        foreach ($components as $component) {
            $apps[] = array(
                'id' => $component->option,
                'name' => $component->getProfileName(),
                'description' => $component->getProfileDescription(),
                'enabled' => $component->enabledForActor($this->getItem()),
            );
        }    
        
        $content = $this->getView()
        ->set('data', $apps)
        ->set('pagination', array(
            'offset' => 0,
            'limit' => 20,
            'total' => count($apps),
        ))
        ->layout('apps')
        ->display();
        
        $context->response->setContent($content);
    }
    
    protected function _actionGetPermissions(AnCommandContext $context)
    {
        $data = array();
        $actor = $this->getItem();
        $components = $this->getItem()->components;
        
        foreach ($components as $component) {
            if (! $component->isAssignable()) {
                continue;
            }

            if (! count($component->getPermissions())) {
                continue;
            }
            
            foreach ($component->getPermissions() as $identifier => $actions) {
                if (strpos($identifier, '.') === false) {
                    $name = $identifier;
                    $identifier = clone $component->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;
                }
                
                $identifier = $this->getIdentifier($identifier);
                
                foreach ($actions as $action) {
                    $key = $identifier->package.':'.$identifier->name.':'.$action;
                    $value = $actor->getPermission($key);
                    $permissions[] = array('name' => $key, 'value' => $value);
                }
                
                $data[] = array(
                    'id' => $component->id,
                    'name' => $component->component, 
                    'enabled' => true, 
                    'permissions' => $permissions
               );
            }        
        }
        
        $content = $this->getView()
        ->set('data', $data)
        ->set('pagination', array(
            'offset' => 0,
            'limit' => 20,
            'total' => count($data),
        ))
        ->layout('permissions')
        ->display();
        
        $context->response->setContent($content);
    }
    
    protected function _actionGetPrivacy(AnCommandContext $context)
    {        
        $data = array(
            'allowFollowRequest' => (bool) $this->getItem()->allowFollowRequest,
            'permissions' => array(
                'name' => 'access',
                'value' => $this->getItem()->access,
            ),
        );
        
        $content = $this->getView()
        ->set('data', $data)
        ->layout('privacy')
        ->display();
        
        $context->response->setContent($content);
    }

    /**
     * Add App.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionAddApp(AnCommandContext $context)
    {
        $data = $context->data;
        $this->getItem()->components->insert($data->app);
    }

    /**
     * Remove App.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionRemoveApp(AnCommandContext $context)
    {
        $data = $context->data;
        $this->getItem()->components->extract($data->app);
    }
    
    public function fetchApp(AnCommandContext $context) 
    {
        $data = $context->data;
        
        $component = $this->getService('repos:components.component')->fetch(array(
            'component' => $data->app
        ));
        
        $content = json_encode(array(
            'id' => $component->option,
            'name' => $component->getProfileName(),
            'description' => $component->getProfileDescription(),
            'enabled' => $component->enabledForActor($this->getItem()),
        ));
        
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
    
    public function canGetapps()
    {
        if ($this->getItem()) {
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
    
    public function canGetpermissions()
    {
        if ($this->getItem()) {
            error_log('Can Edit Permissions');
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
    
    public function canGetprivacy()
    {
        if ($this->getItem()) {
            error_log('Can Edit Privacy');
            return $this->getItem()->authorize('edit');
        }

        return false;
    }
}
