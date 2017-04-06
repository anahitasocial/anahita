<?php

/**
 * Person Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerPerson extends ComActorsControllerDefault
{
    protected $_allowed_user_types;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback('after.add', array($this, 'mailActivationLink'));
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
            'behaviors' => array(
                'validatable',
                'com:mailer.controller.behavior.mailer'
            ),
            'request' => array(
                'reset_password' => 0
            )
        ));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, 'ownable');

        $this->_allowed_user_types = array(
            ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR,
            ComPeopleDomainEntityPerson::USERTYPE_REGISTERED,
        );

        $viewer = get_viewer();

        if ($viewer->superadmin()) {
            $this->_allowed_user_types[] = ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR;
        }
    }

    /**
     * Browse Action.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return ComPeopleDomainEntityPerson
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        if (!$context->query) {
            $context->query = $this->getRepository()->getQuery();
        }

        $query = $context->query;

        if ($this->filter) {

            if (
                $this->filter['usertype'] &&
                in_array($this->filter['usertype'], $this->_allowed_user_types)
                ) {
                $query->filterUsertype($this->getService('koowa:filter.cmd')
                      ->sanitize($this->filter['usertype']));
            }

            if ($this->filter['disabled']) {
                $query->filterDisabledAccounts(true);
            }
        }

        if ($this->getService('koowa:filter.email')->validate($this->q)) {
            $query->filterEmail($this->q);
        }

        if ($this->getService('com:people.filter.username')->validate($this->q)) {
            $query->filterUsername($this->q);
        }

        if ($this->q) {
            $query->keyword = $this->getService('anahita:filter.term')->sanitize($this->q);
        }

        if ($this->ids) {
            $ids = KConfig::unbox($this->ids);
            $query->id($ids);
        } else {
            $query->limit($this->limit, $this->start);
        }

        $entities = $this->getState()->setList($query->toEntityset())->getList();

        //print str_replace('#_', 'jos', $entities->getQuery());

        return $entities;
    }

    protected function _actionPost(KCommandContext $context)
    {
        dispatch_plugin('user.onBeforeSavePerson', array('data' => $context->data));

        $person = parent::_actionPost($context);

        dispatch_plugin('user.onAfterSavePerson', array('person' => $person));
    }

    /**
     * Edit a person's data and synchronize with the person with the user entity.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit(KCommandContext $context)
    {
        $data = $context->data;

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

        //add the validations here
        $this->getRepository()
        ->getValidator()
        ->addValidation('username', 'uniqueness')
        ->addValidation('email', 'uniqueness');

        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }

        //now check to see if usertype can be set, otherwise the value is unchanged
        if (in_array($usertype, $this->_allowed_user_types) && $person->authorize('changeUsertype')) {
            $person->usertype = $usertype;
        }

        $person->timestamp();

        $this->setMessage('LIB-AN-PROMPT-UPDATE-SUCCESS', 'success');

        $edit = ($data->password && $data->username) ? 'account' : $this->edit;
        $url = sprintf($person->getURL('false')."&get=settings&edit=%s", $edit);
        $context->response->setRedirect(route($url));

        return $person;
    }

    /**
     * Person add action creates a new person object.
     *
     * @param KCommandContext $context Commaind chain context
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd(KCommandContext  $context)
    {
        $data = $context->data;

        $isFirstUser = !(bool) $this->getService('repos:people.person')
                                    ->getQuery(true)
                                    ->fetchValue('id');

        $person = parent::_actionAdd($context);

        $this->getRepository()
        ->getValidator()
        ->addValidation('username', 'uniqueness')
        ->addValidation('email', 'uniqueness');

        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }

        $viewer = get_viewer();

        if ($isFirstUser) {
            $person->usertype = ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR;
        } elseif ($viewer->admin() && in_array($data->usertype, $this->_allowed_user_types)) {
            $person->usertype = $data->usertype;
        } else {
            $person->usertype = ComPeopleDomainEntityPerson::USERTYPE_REGISTERED;
        }

        $redirectUrl = 'option=com_people';

        if ($isFirstUser) {
            $this->registerCallback('after.add', array($this, 'activateFirstAdmin'));
        } elseif ($viewer->admin()) {
            $redirectUrl .= '&view=people';
            if ($person->admin()) {
                $this->registerCallback('after.add', array($this, 'mailAdminsNewAdmin'));
            }
        } else {
            $redirectUrl .= '&view=session';
            $context->response->setHeader('X-User-Activation-Required', true);
            $this->setMessage(AnTranslator::sprintf('COM-PEOPLE-PROMPT-ACTIVATION-LINK-SENT', $person->name), 'success');
        }

        $context->response->setRedirect(route($redirectUrl));
        $context->response->status = 200;

        return $person;
    }

    /**
     * Deletes an actor and all of the necessary cleanup. It also dispatches all the apps to
     * clean up after the deleted actor.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete(KCommandContext $context)
    {
        dispatch_plugin('user.onBeforeDeletePerson', array('data' => $context->data));

        $person = parent::_actionDelete($context);

        $this->getService('repos:sessions.session')->destroy(array('nodeId' => $person->id));

        dispatch_plugin('user.onAfterDeletePerson', array('person' => $person));

        return $person;
    }

    /**
     * Set the necessary redirect.
     *
     * @param KCommandContext $context
     */
    public function redirect(KCommandContext $context)
    {
        $url = null;

        if ($context->action === 'delete') {

            $viewer = $this->getService('com:people.viewer');

            if ($viewer->id == $this->getItem()->id) {
                $url = 'index.php?';
            } else {
                $url = 'option=com_people&view=people';
            }
        }

        if ($url) {
            $context->response->setRedirect(route($url));
        }
    }

    /**
     * Mail an activation link.
     *
     * @param KCommandContext $context The context parameter
     */
    public function mailActivationLink(KCommandContext $context)
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

        $settings = $this->getService('com:settings.setting');

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
     * @param KCommandContext $context The context parameter
     */
    public function mailAdminsNewAdmin(KCommandContext $context)
    {
        $person = $context->result;

        $this->mailAdmins(array(
            'subject' => AnTranslator::sprintf('COM-PEOPLE-MAIL-SUBJECT-NEW-ADMIN', $person->name),
            'template' => 'new_admin',
        ));
    }

    /**
     * Autologin the first user which is also the first super admin.
     *
     * @param KCommandContext $context The context parameter
     */
    public function activateFirstAdmin(KCommandContext $context)
    {
        $person = $context->result;
        $url = route('option=com_people&view=session&token='.$person->activationCode);
        $context->response->setRedirect($url);
    }

    /**
     * Called before the setting page is displayed.
     *
     * @param KEvent $event
     */
    public function onSettingDisplay(KEvent $event)
    {
        $tabs = $event->tabs;
        $viewer = get_viewer();

        if ($viewer->admin() || $viewer->eql($this->getItem())) {
            $tabs->insert('account', array('label' => AnTranslator::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));
        }
    }
}
