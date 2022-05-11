<?php

/**
 * Administrable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
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
                array('before.addAdmin', 'before.removeAdmin'),
                array($this, 'fetchAdmin')
        );
    }
    
    /**
     * Get Candidates.
     *
     * @param AnCommandContext $context Context parameter
     */
     protected function _actionGetadmins(AnCommandContext $context)
     {
         $data = $this->getItem()->administrators
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
      * Get Candidates.
      *
      * @param AnCommandContext $context Context parameter
      */
     protected function _actionGetAdminsCandidates(AnCommandContext $context)
     {
         $data = $context->data;
         
         $data = $this->getItem()
         ->getAdminCanditates()
         ->keyword($this->q)
         ->limit($this->limit, 0)
         ->toArray();

         $this->getView()
         ->set('data', $data)
         ->set('pagination', array(
             'offset' => 0,
             'limit' => $this->limit,
             'total' => count($data),
         ));
         return $data;
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
        
        $content = json_encode($this->admin->toSerializableArray());
        $context->response->setContent($content);
    }

    /**
     * Get Candidates. Soon to be legacy
     *
     * @param AnCommandContext $context Context parameter
     * @deprecated use _actionGetAdminsCandidates instead  
     */
    protected function _actionGetCandidates(AnCommandContext $context)
    {
        $data = $context->data;
        $canditates = $this->getItem()->getAdminCanditates();
        $canditates->keyword($this->q)->limit($this->limit, 0);
        $people = array();

        foreach ($canditates as $person) {
            $people[] = array(
              'id' => $person->id,
              'value' => $person->name, );
        }

        $this->getView()->set($people);

        return $people;
    }

    /**
     * Get settings.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionGetSettings(AnCommandContext $context)
    {
        $entity = $this->getItem();
        $dispatcher = $this->getService('anahita:event.dispatcher');
        
        $entity->components->registerEventDispatcher($dispatcher);
        $dispatcher->addEventListener('onSettingDisplay', $this->_mixer);
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
            $repo = 'repos:people.person';
            $admin = $this->getService($repo)->fetch($data->adminid);
            $this->getState()->admin = $admin;
        }
    }
}
