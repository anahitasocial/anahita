<?php

/**
 * Session Controller. Manages a session of a person.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerSession extends ComBaseControllerResource
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback(
            'after.add',
            array($this, 'redirect'),
            array('url' => $config->redirect_to_after_login));

        $this->registerCallback(
            'after.delete',
            array($this, 'redirect'),
            array('url' => $config->redirect_to_after_logout));
    }

    /**
     * Set the request information.
     *
     * @param array An associative array of request information
     *
     * @return LibBaseControllerAbstract
     */
    public function setRequest(array $request)
    {
        parent::setRequest($request);

        if (isset($this->_request->return)) {
            $return = $this->getService('com:people.filter.return')
                           ->sanitize($this->_request->return);
            $this->_request->return = $return;
            $this->return = $return;
        }

        return $this;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * you can set the redirect url for when a user is logged in
     * as follow
     *
     * <code>
     * KService::setConfig('com:people.controller.session', array(
     *  'redirect_to_after_login'  => 'mynewurl'
     *  'redirect_to_after_logout' => 'mynewurl'
     * ));
     * </code>
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'redirect_to_after_login' => '',
            'redirect_to_after_logout' => '',
            //by default the format is json
            'request' => array(
                'format' => 'json',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the session.
     *
     * @param KCommandContext $context Command chain context
     */
    protected function _actionRead(KCommandContext $context)
    {
        $viewer = get_viewer();

        $this->_state->setItem($viewer);

        if (isset($_SESSION['return'])) {
            $this->_state->append(array(
                'return' => $this->getService('com:people.filter.return')->sanitize($_SESSION['return'])
            ));
        }

        return $viewer;
    }

    /**
     * Post method.
     *
     * @param KCommandContext $context
     */
    protected function _actionPost(KCommandContext $context)
    {
        try {
            $result = $this->execute('add', $context);
            return $result;
        } catch (RuntimeException $e) {
            $context->response->setRedirect(route('option=com_people&view=session'));
            throw $e;
        }
    }

    /**
     * Authenticate a person and create a new session If a username password is passed then the user is first logged in.
     *
     * @param KCommandContext $context Command chain context
     *
     * @throws LibBaseControllerExceptionUnauthorized If authentication failed
     * @throws LibBaseControllerExceptionForbidden    If person is authenticated but forbidden
     * @throws RuntimeException                       for unkown error
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $data = $context->data;

        if ($data->return) {
            $_SESSION['return'] = $this->getService('com:people.filter.return')->sanitize($data->return);
            $context->url = base64UrlDecode($data->return);
        } else {
            $_SESSION['return'] = null;
        }

        $credentials = array(
            'username' => $data->username,
            'password' => $data->password,
            'remember' => $data->remember
        );

        $response = $this->getService('com:people.authentication.response');
        dispatch_plugin('authentication.onAuthenticate', array(
                            'credentials' => $credentials,
                            'response' => $response
                        ));

        if ($response->status === ComPeopleAuthentication::STATUS_SUCCESS) {

            $credentials['username'] = $response->username;
            $credentials['password'] = $response->password;

            $this->getService('com:people.helper.person')->login($credentials, $credentials['remember']);

            $this->getResponse()->status = KHttpResponse::CREATED;

            if(!$context->url) {
                $context->url = route(array(
                    'option' => 'com_people',
                    'view' => 'person',
                    'uniqueAlias' => $credentials['username']
                ));
            }

            $this->getResponse()->setRedirect($context->url);
            $_SESSION['return'] = null;

        } else {

            $this->setMessage('COM-PEOPLE-AUTHENTICATION-FAILED', 'error');
            throw new LibBaseControllerExceptionUnauthorized('Authentication Failed. Check username/password');
            $this->getResponse()->status = KHttpResponse::FORBIDDEN;
            $this->getResponse()->setRedirect(route('option=com_people&view=session'));
        }

        return true;
    }

    /**
     * Deletes a session and logs out the user.
     *
     * @param KCommandContext $context Command chain context
     */
    protected function _actionDelete(KCommandContext $context)
    {
        $this->getService('com:people.helper.person')->logout();
        $context->response->setRedirect(route('index.php?'));
    }

    /**
     * Logs in a user if an activation token is provided.
     *
     * @param KCommandContext $context Command chain context
     *
     * @return bool true on success
     */
    protected function _actionTokenlogin(KCommandContext $context)
    {
        if ($this->token == '') {
            throw new AnErrorException(array('No token is provided'), KHttpResponse::FORBIDDEN);
        }

        $person = $this->getService('repos:people.person')->find(array('activationCode' => $this->token));

        if (! $person) {
            throw new AnErrorException(array('This token is invalid'), KHttpResponse::NOT_FOUND);
        }

        $newPerson = ($person->registrationDate->compare($person->lastVisitDate)) ? true : false;

        if ($newPerson) {
            $person->enable();
        }

        $person->activationCode = '';
        $this->token = null;
        $this->_request->token = null;

        if ($this->reset_password) {
            $_SESSION['reset_password_prompt'] = 1;
        }

        $credentials = array(
            'username' => $person->username,
            'password' => $person->password,
            'remember' => true,
        );

        $this->getService('com:people.helper.person')->login($credentials, $credentials['remember']);

        if ($this->return) {
            $_SESSION['return'] = $this->getService('com:people.filter.return')->sanitize($this->return);
            $returnUrl = base64UrlDecode($this->return);
            $this->getResponse()->setRedirect($returnUrl);
        } else {
            $_SESSION['return'] = null;
            $this->setMessage('COM-PEOPLE-PROMPT-UPDATE-PASSWORD');
            $this->getResponse()->setRedirect(route($person->getURL().'&get=settings&edit=account'));
        }

        $this->getResponse()->status = KHttpResponse::ACCEPTED;

        return true;
    }
}
