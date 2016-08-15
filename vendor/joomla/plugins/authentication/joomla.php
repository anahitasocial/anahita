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
    public $name;

    function onAuthenticate(&$credentials, $options, &$response)
    {
        if(empty($credentials['password']))
        {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'Empty password not allowed';
            return false;
        }

        $condition = strpos($credentials['username'],'@') ? 'email' : 'username';
        $person = KService::get('repos:people.person')->find(array(
            $condition => $credentials['username']
        ));

        if(isset($person))
        {
            $credentials['username'] = $person->username;
            $parts  = explode(':', $person->password);
            $crypt  = $parts[0];
            $salt   = isset($parts[1]) ? $parts[1] : '';

            jimport('joomla.user.helper');
            $testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

            if($crypt === $testcrypt)
            {
                $response->id = $person->id;
                $response->username = $person->username;
                $response->email = $person->email;
                $response->status = JAUTHENTICATE_STATUS_SUCCESS;
                $response->error_message = '';
            }
            else
            {
                $response->status = JAUTHENTICATE_STATUS_FAILURE;
                $response->error_message = 'Invalid password';
            }
        }
        else
        {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'User does not exist';
        }
    }
}
