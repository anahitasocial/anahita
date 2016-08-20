<?php

/**
 * Joomla Authentication plugin
 *
 * @package     Joomla
 * @subpackage  JFramework
 * @since 1.5
 */
class plgAuthenticationJoomla extends PlgAnahitaDefault
{
    function onAuthenticate(KEvent $event)
    {
        $credentials = $event->credentials;
        $response = $event->response;

        if (empty($credentials->username)) {
            $response->status = ComPeopleAuthentication::STATUS_FAILURE;
            $response->error_message = 'Empty password not allowed';
            return false;
        }

        $uniqueAlias = strpos($credentials->username, '@') ? 'email' : 'username';

        $person = KService::get('repos:people.person')->find(array(
            $uniqueAlias => $credentials->username
        ));

        if (isset($person)) {

            $credentials->username = $person->username;
            $parts = explode(':', $person->password);
            $crypt = $parts[0];
            $salt = isset($parts[1]) ? $parts[1] : '';

            jimport('joomla.user.helper');
            $testcrypt = JUserHelper::getCryptedPassword($credentials->password, $salt);

            if ($crypt === $testcrypt) {

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
