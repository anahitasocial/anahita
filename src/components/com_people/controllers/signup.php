<?php

/**
 * Signup Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerSignup extends ComBaseControllerService
{
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
                'identifiable' => array(
                    'repository' => 'repos:people.person'
                ),
                'validatable',
                'com:mailer.controller.behavior.mailer',
            ),
            'serviceable' => array(
                'except' => array(
                    'browse',
                    'edit',
                    'delete',
                )
            ),
        ));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, array('ownable'));
    }
    
    /**
     * Person signup action creates a new person object.
     *
     * @param AnCommandContext $context Commaind chain context
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd(AnCommandContext $context)
    {
        $data = $context->data;
        
        dispatch_plugin('user.onBeforeAddPerson', array('data' => $context->data));

        $isFirstUser = !(bool) $this->getService('repos:people.person')
                                    ->getQuery(true)
                                    ->fetchValue('id');
        
        $person = parent::_actionAdd($context);

        $person->requiresActivation();

        if ($isFirstUser) {
            $person->usertype = ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR;
        } else {
            $person->usertype = ComPeopleDomainEntityPerson::USERTYPE_REGISTERED;
        }
        
        if (! $person->validate()) {
            error_log(print_r($person->getErrors()->getMessage(), true));
            throw new AnErrorException($person->getErrors(), AnHttpResponse::BAD_REQUEST);
        }

        dispatch_plugin('user.onAfterAddPerson', array('person' => $person));
        
        if ($isFirstUser) {
            $this->registerCallback('after.add', array($this, 'activateFirstAdmin'));
        } else {
            $this->registerCallback('after.add', array($this, 'mailActivationLink'));
            $context->response->setHeader('X-User-Activation-Required', true);
            $this->setMessage(AnTranslator::sprintf('COM-PEOPLE-PROMPT-ACTIVATION-LINK-SENT', $person->name), 'success');
        }
        
        $this->getResponse()->status = AnHttpResponse::OK;

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
        $settings = $this->getService('com:settings.config');
        $subject = 'COM-PEOPLE-MAIL-SUBJECT-ACCOUNT-ACTIVATE';
        
        $mails[] = array(
            'to' => $person->email,
            'subject' => sprintf(AnTranslator::_($subject), $settings->sitename),
            'template' => 'account_activate',
            'data' => array(
                'person' => $person,
            ),
        );

        $this->mail($mails);
    }
    
    /**
     * Autologin the first user which is also the first super admin.
     *
     * @param AnCommandContext $context The context parameter
     */
    public function activateFirstAdmin(AnCommandContext $context)
    {
        $person = $context->result;
        $url = route('option=com_people&view=session&isFirstPerson=1&token='.$person->activationCode);
        $context->response->setRedirect($url);
    }
}