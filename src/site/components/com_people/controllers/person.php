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
            'behaviors' => array('validatable', 'com://site/mailer.controller.behavior.mailer'),
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

        if ($this->getService('com://site/people.filter.username')->validate($this->q)) {
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
        $userType = null;
        if ($data->userType) {
            $userType = $data->userType;
            unset($context->data->userType);
        }

        $person = parent::_actionEdit($context);

        //just to make sure password is set
        if($data->password) {
           $person->setPassword($data->password);
        }

        //add the validations here
        $this->getRepository()
        ->getValidator()
        ->addValidation('username', 'uniqueness')
        ->addValidation('email', 'uniqueness');

        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
        }

        //now check to see if usertype can be set, otherwise the value is unchanged
        if(in_array($userType, $this->_allowed_user_types) && $person->authorize('changeUserType')) {
            $person->userType = $userType;
        }

        $person->timestamp();
        $this->setMessage('LIB-AN-PROMPT-UPDATE-SUCCESS', 'success');

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
        $viewer = get_viewer();
        $firsttime = !(bool) $this->getService('repos://site/users')
                                  ->getQuery(true)
                                  ->fetchValue('id');

        $person = parent::_actionAdd($context);

        //just to make sure password is set
        if($data->password) {
           $person->setPassword($data->password);
        }

        $redirectUrl = 'option=com_people';

        $this->getRepository()
        ->getValidator()
        ->addValidation('username', 'uniqueness')
        ->addValidation('email', 'uniqueness');

        if ($person->validate() === false) {
            throw new AnErrorException($person->getErrors(), KHttpResponse::BAD_REQUEST);
            return false;
        }

        if ($viewer->admin() && in_array($data->userType, $this->_allowed_user_types)) {
            $person->userType = $data->userType;
        } else {
            $person->userType = ComPeopleDomainEntityPerson::USERTYPE_REGISTERED;
        }

        if ($firsttime) {
          $this->registerCallback('after.add', array($this, 'activateFirstAdmin'));
        } elseif ($viewer->admin()) {
            $redirectUrl .= '&view=people';
            if ($person->admin()) {
                $this->registerCallback('after.add', array($this, 'mailAdminsNewAdmin'));
            }
        } else {
            $context->response->setHeader('X-User-Activation-Required', true);
            $this->setMessage(JText::sprintf('COM-PEOPLE-PROMPT-ACTIVATION-LINK-SENT', $person->name), 'success');
            $redirectUrl .= '&view=session';
        }

        $context->response->setRedirect(JRoute::_($redirectUrl));
        $context->response->status = 200;

        return $person;
    }

    /**
     * Deletes a person and all of their assets. It also logsout the person.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionDelete(KCommandContext $context)
    {
        parent::_actionDelete($context);

        $this->commit();

        $userId = $this->getItem()->userId;

        JFactory::getApplication()->logout($userId);
        JFactory::getUser($userId)->delete();
    }

    /**
     * Set the necessary redirect.
     *
     * @param KCommandContext $context
     */
    public function redirect(KCommandContext $context)
    {
        $url = null;

        if ($context->action == 'delete') {
            $url = 'option=com_people&view=people';
        }

        if ($url) {
            $context->response->setRedirect(JRoute::_($url));
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
        $this->user = $person->getUserObject();
        $viewer = get_viewer();

        if ($viewer->admin()) {
            $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-CREATED';
            $template = 'account_created';
        } else {
            $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-ACTIVATE';
            $template = 'account_activate';
        }

        $this->mail(array(
            'to' => $person->email,
            'subject' => sprintf(JText::_($subject), JFactory::getConfig()->getValue('sitename')),
            'template' => $template,
        ));
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
            'subject' => JText::sprintf('COM-PEOPLE-MAIL-SUBJECT-NEW-ADMIN', $person->name),
            'template' => 'new_admin',
        ));
    }

    /**
    * Autologin the first user which is also the first super admin
    *
    * @param KCommandContext $context The context parameter
    */
    public function activateFirstAdmin(KCommandContext $context)
    {
        $person = $context->result;
        $user = $this->getService('repos://site/users.user')
                     ->find(array('id' => $person->userId));
        $context->response
        ->setRedirect(JRoute::_('option=com_people&view=session&token='.$user->activation));
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
            $tabs->insert('account', array('label' => JText::_('COM-PEOPLE-SETTING-TAB-ACCOUNT')));
        }
    }
}
