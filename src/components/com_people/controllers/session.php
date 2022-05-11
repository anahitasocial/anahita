<?php

/**
 * Session Controller. Manages a session of a person.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComPeopleControllerSession extends ComBaseControllerResource
{
    /**
     * Return the session.
     *
     * @param AnCommandContext $context Command chain context
     */
    protected function _actionRead(AnCommandContext $context)
    {
        if (! $this->_state->viewer->guest()) {
            $this->_state->setItem($this->_state->viewer);
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
            'password' => $data->password,
        );
        $response = $this->getService('com:people.authentication.response');
        $config = array(
            'credentials' => $credentials,
            'response' => $response
        );
        
        dispatch_plugin('authentication.onAuthenticate', $config);

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
