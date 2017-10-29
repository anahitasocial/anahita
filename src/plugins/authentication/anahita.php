<?php

class plgAuthenticationAnahita extends PlgAnahitaDefault
{
    public function onAuthenticate(KEvent $event)
    {
        $credentials = $event->credentials;
        $response = $event->response;

        if (empty($credentials->username)) {
            $response->status = ComPeopleAuthentication::STATUS_FAILURE;
            $response->error_message = 'Empty username not allowed';
            return false;
        }

        if (empty($credentials->password)) {
            $response->status = ComPeopleAuthentication::STATUS_FAILURE;
            $response->error_message = 'Empty password not allowed';
            return false;
        }

        $uniqueAlias = strpos($credentials->username, '@') ? 'email' : 'username';

        $person = KService::get('repos:people.person')->find(array(
            $uniqueAlias => $credentials->username,
            'enabled' => 1
        ));

        if (! is_null($person)) {

            $success = false;

            //check for legacy password
            if (strpos($person->password, ':')) {

                $credentials->username = $person->username;
                $parts = explode(':', $person->password);
                $crypt = $parts[0];
                $salt = isset($parts[1]) ? $parts[1] : '';

                if ($person->password === md5($credentials->password.$salt).':'.$salt) {
                    $success = true;
                    //legacy password will be upgraded to php's new password_hash()
                    $person->password = $credentials->password;
                }

            } else {
                $success = password_verify($credentials->password, $person->password);
            }

            if ($success) {
                $response->username = $person->username;
                $response->email = $person->email;
                $response->status = ComPeopleAuthentication::STATUS_SUCCESS;
                $response->error_message = '';
            } else {
                $response->status = ComPeopleAuthentication::STATUS_FAILURE;
                $response->error_message = 'Invalid password';
            }

        } else {
            $response->status = ComPeopleAuthentication::STATUS_FAILURE;
            $response->error_message = 'User does not exist';
        }

        return $response;
    }
}
