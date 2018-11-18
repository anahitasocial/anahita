<?php

/**
 * Token Controller. Performs password RESTful operation for reseting a token.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerToken extends ComBaseControllerResource
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback('after.add', array($this, 'mailConfirmation'));
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
            'behaviors' => array('com:mailer.controller.behavior.mailer'),
        ));

        parent::_initialize($config);
    }

    /**
     * Dispatches a correct action based on the state.
     *
     * @param AnCommandContext $context
     */
    protected function _actionPost(AnCommandContext $context)
    {
        return $this->execute('add', $context);
    }

    /**
     * Resets a password.
     *
     * @param AnCommandContext $context
     */
    protected function _actionAdd(AnCommandContext $context)
    {
        $data = $context->data;
        $email = $data->email;
        //an email
        $person = $this->getService('repos:people.person')
                      ->getQuery()
                      ->email($email)
                      ->where('IF(!@col(enabled),@col(activationCode) <> \'\',1)')
                      ->disableChain()
                      ->fetch();

        if ($person) {
            $person->requiresReactivation()->save();
            $this->getResponse()->status = AnHttpResponse::CREATED;
            $this->person = $person;
        } else {
            throw new LibBaseControllerExceptionNotFound('Email Not Found');
        }
    }

    /**
     * Send an email confirmation after reset.
     *
     * @param AnCommandContext $context
     */
    public function mailConfirmation(AnCommandContext $context)
    {
        if ($this->person) {
            $settings = $this->getService('com:settings.setting');
            $mails[] = array(
                'to' => $this->person->email,
                'subject' => sprintf(AnTranslator::_('COM-PEOPLE-MAIL-SUBJECT-PASSWORD-RESET'), $settings->sitename),
                'template' => 'password_reset',
            );
            $this->mail($mails);
        }
    }
}
