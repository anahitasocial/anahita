<?php

/**
 * Person Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleControllerPerson extends ComActorsControllerDefault
{
    protected $_allowed_user_types;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback('after.add', array($this, 'mailActivationLink'));
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
            'behaviors' => array(
                'validatable',
                'com:mailer.controller.behavior.mailer'
            ),
            'request' => array(
                'reset_password' => 0,
                'edit' => 'profile'
            )
        ));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, 'ownable');

        $this->_allowed_user_types = array(
            ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR,
            ComPeopleDomainEntityPerson::USERTYPE_REGISTERED,
        );

        $viewer = $this->getService('com:people.viewer');

        if ($viewer->superadmin()) {
            $this->_allowed_user_types[] = ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR;
        }
    }

    /**
     * Browse Action.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return ComPeopleDomainEntityPerson
     */
    protected function _actionBrowse(AnCommandContext $context)
    {
        if (! $context->query) {
            $context->query = $this->getRepository()->getQuery();
        }

        $query = $context->query;

        if ($this->viewer->admin()) {
            if ($this->filter) {
                if ($this->filter['usertype']) {
                    $query->filterUsertype($this->getService('anahita:filter.cmd')
                          ->sanitize($this->filter['usertype']));
                }

                if ($this->filter['disabled']) {
                    $query->filterDisabledAccounts(true);
                }
            }
            
            if ($this->q) {
                if ($this->getService('anahita:filter.email')->validate($this->q)) {
                    $query->filterEmail($this->q);
                }
            }
        }
        
        if ($this->getService('com:people.filter.username')->validate($this->q)) {
            $query->filterUsername($this->q);
        }
        
        if ($this->q) {
            $query->keyword = $this->getService('anahita:filter.term')->sanitize($this->q);
        }

        if ($this->ids) {
            $ids = AnConfig::unbox($this->ids);
            $query->id($ids);
        } else {
            $query->limit($this->limit, $this->start);
        }

        $entities = $this->getState()->setList($query->toEntityset())->getList();

        return $entities;
    }

    /**
     * Edit a person's data and synchronize with the person with the user entity.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit(AnCommandContext $context)
    {
        $viewer = $this->getService('com:people.viewer');
        $data = $context->data;

        dispatch_plugin('user.onBeforeEditPerson', array('data' => $context->data));

        //dont' set the usertype yet, until we find the conditions are met
        $usertype = null;

        if ($data->usertype) {
            $usertype = $data->usertype;
            unset($context->data->usertype);
        }

        if ($data->password) {
            $_SESSION['reset_password_prompt'] = 0;
        }

        $person = parent::_actionEdit($context);

        if (! $person->validate()) {
            throw new AnErrorException($person->getErrors(), AnHttpResponse::BAD_REQUEST);
        }

        //now check to see if usertype can be set, otherwise the value is unchanged
        if (in_array($usertype, $this->_allowed_user_types) && $person->authorize('changeUsertype')) {
            $person->usertype = $usertype;
            
            if ($person->admin()) {
                $this->registerCallback('after.edit', array($this, 'mailAdminsNewAdmin'));
            }
        }

        $person->timestamp();
        
        dispatch_plugin('user.onAfterEditPerson', array('person' => $person));

        return $person;
    }

    /**
     * Person add action creates a new person object.
     *
     * @param AnCommandContext $context Commaind chain context
     *
     * @return AnDomainEntityAbstract
     */
     protected function _actionAdd(AnCommandContext  $context)
     {
         $data = $context->data;

         dispatch_plugin('user.onBeforeAddPerson', array('data' => $context->data));
         
         $data->password = bin2hex(openssl_random_pseudo_bytes(32));
         
         $person = parent::_actionAdd($context);

         if (in_array($data->usertype, $this->_allowed_user_types)) {
             $person->usertype = $data->usertype;
         } else {
             $person->usertype = ComPeopleDomainEntityPerson::USERTYPE_REGISTERED;
         }
         
         $person->requiresActivation();

         if (! $person->validate()) {
             // error_log(print_r($person->getErrors()->getMessage(), true));
             throw new AnErrorException($person->getErrors(), AnHttpResponse::BAD_REQUEST);
         }

         dispatch_plugin('user.onAfterAddPerson', array('person' => $person));

         if ($person->admin()) {
             $this->registerCallback('after.add', array($this, 'mailAdminsNewAdmin'));
         }
         
         return $person;
     }

    /**
     * Deletes an actor and all of the necessary cleanup. It also dispatches all the apps to
     * clean up after the deleted actor.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        dispatch_plugin('user.onBeforeDeletePerson', array('data' => $context->data));

        $person = parent::_actionDelete($context);
        
        $person_id = $person->id;

        $this->getService('repos:sessions.session')->destroy(array('nodeId' => $person->id));

        dispatch_plugin('user.onAfterDeletePerson', array('id' => $person_id));

        return $person;
    }

    /**
     * Mail an activation link.
     *
     * @param AnCommandContext $context The context parameter
     */
    public function mailActivationLink(AnCommandContext $context)
    {
        $person = $context->result;
        $viewer = get_viewer();

        if ($viewer->admin()) {
            $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-CREATED';
            $template = 'account_created';
        } else {
            $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-ACTIVATE';
            $template = 'account_activate';
        }

        $settings = $this->getService('com:settings.config');

        $mails[] = array(
            'to' => $person->email,
            'subject' => sprintf(AnTranslator::_($subject), $settings->sitename),
            'template' => $template,
        );

        $this->mail($mails);
    }

    /**
     * Notify admins that a new admin has joined the network.
     *
     * @param AnCommandContext $context The context parameter
     */
    public function mailAdminsNewAdmin(AnCommandContext $context)
    {
        $person = $context->result;

        $this->mailAdmins(array(
            'subject' => AnTranslator::sprintf('COM-PEOPLE-MAIL-SUBJECT-NEW-ADMIN', $person->name),
            'template' => 'new_admin',
        ));
    }

    /**
     * Called before the setting page is displayed.
     *
     * @param AnEvent $event
     */
    public function onSettingDisplay(AnEvent $event)
    {
        $tabs = $event->tabs;
        $viewer = get_viewer();

        if ($viewer->admin() || $viewer->eql($this->getItem())) {
            $tabs->insert('account', array('label' => AnTranslator::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));
        }
    }
}
