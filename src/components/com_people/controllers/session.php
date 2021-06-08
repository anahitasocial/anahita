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
     * Initializes the default configuration for the object.
     *
     * you can set the redirect url for when a user is logged in
     * as follow
     *
     * <code>
     * AnService::setConfig('com:people.controller.session', array(
     *  'redirect_to_after_login'  => 'mynewurl'
     *  'redirect_to_after_logout' => 'mynewurl'
     * ));
     * </code>
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'request' => array(
                'format' => 'json',
                'isFirstPerson' => 0,
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the session.
     *
     * @param AnCommandContext $context Command chain context
     */
    protected function _actionRead(AnCommandContext $context)
    {
        if (! $this->_state->viewer->guest()) {
            $this->_state->setItem($this->_state->viewer);
            $this->getResponse()->setRedirect(route($this->_state->viewer->getURL()));
        }

        return $this->_state->viewer;
    }

    /**
     * Post method.
     *
     * @param AnCommandContext $context
     */
    protected function _actionPost(AnCommandContext $context)
    {
        return $this->execute('add', $context);
    }

    /**
     * Authenticate a person and create a new session If a username password is passed then the user is first logged in.
     *
     * @param AnCommandContext $context Command chain context
     *
     * @throws LibBaseControllerExceptionUnauthorized If authentication failed
     * @throws LibBaseControllerExceptionForbidden    If person is authenticated but forbidden
     * @throws RuntimeException                       for unkown error
     */
    protected function _actionAdd(AnCommandContext $context)
    {
        $data = $context->data;

        dispatch_plugin('user.onBeforeLoginPerson', array('credentials' => $data));

        $credentials = array(
            'username' => $data->username,
            'password' => $data->password
        );

        $response = $this->getService('com:people.authentication.response');

        dispatch_plugin('authentication.onAuthenticate', array(
                            'credentials' => $credentials,
                            'response' => $response
                        ));

        if ($response->status === ComPeopleAuthentication::STATUS_SUCCESS) {
            $person = $this->getService('com:people.helper.person')->login($credentials);
            $this->getState()->setItem($person);
            $this->getResponse()->status = AnHttpResponse::CREATED;
        } else {
            throw new LibBaseControllerExceptionUnauthorized('Authentication Failed. Check username/password');
            $this->getResponse()->status = AnHttpResponse::FORBIDDEN;
        }
    }

    /**
     * Deletes a session and logs out the user.
     *
     * @param AnCommandContext $context Command chain context
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        $viewer = $this->_state->viewer;
        $person_id = $viewer->id;
        dispatch_plugin('user.onBeforeLogoutPerson', array('person' => $viewer));
        $this->getService('com:people.helper.person')->logout($viewer);
        dispatch_plugin('user.onAfterLogoutPerson', array('id' => $person_id));
        $context->response->setRedirect(route('index.php?'));
    }

    /**
     * Logs in a user if an activation token is provided.
     *
     * @param AnCommandContext $context Command chain context
     *
     * @return bool true on success
     */
    protected function _actionTokenlogin(AnCommandContext $context)
    {
        if ($this->token == '') {
            throw new AnErrorException(array('No token is provided'), AnHttpResponse::FORBIDDEN);
        }

        $person = $this->getService('repos:people.person')->find(array('activationCode' => $this->token));

        if (! $person) {
            throw new AnErrorException(array('This token is invalid'), AnHttpResponse::NOT_FOUND);
        }

        $newPerson = ($person->registrationDate->compare($person->lastVisitDate)) ? true : false;

        if ($newPerson) {
            $person->enable();
        }

        $person->activationCode = '';
        $this->token = '';
        $this->_request->token = '';

        if ($this->reset_password) {
            AnRequest::set('session.reset_password_prompt', 1);
        }

        $credentials = array(
            'username' => $person->username,
            'password' => $person->password
        );

        dispatch_plugin('user.onBeforeLoginPerson', array('credentials' => $credentials));

        $this->getService('com:people.helper.person')->login($credentials);
        $this->getState()->setItem($person);
        $this->getResponse()->status = AnHttpResponse::CREATED;

        dispatch_plugin('user.onAfterLoginPerson', array('person' => $person));

        $this->getResponse()->status = AnHttpResponse::ACCEPTED;

        return true;
    }
}
