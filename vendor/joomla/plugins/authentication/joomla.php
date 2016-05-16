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

    /**
     * This method should handle any authentication and report back to the subject
     *
     * @access  public
     * @param   array   $credentials Array holding the user credentials
     * @param   array   $options     Array of extra options
     * @param   object  $response    Authentication response object
     * @return  boolean
     * @since 1.5
     */
    function onAuthenticate(&$credentials, $options, &$response)
    {

        jimport('joomla.user.helper');

        // Joomla does not like blank passwords
        if(empty($credentials['password']))
        {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'Empty password not allowed';
            return false;
        }

        // Initialize variables
        $conditions = '';

        // Get a database object
        $db =& JFactory::getDBO();
        $username = $db->Quote($credentials['username']);

        $query = 'SELECT `id`, `username`, `password`, `email`'
            . ' FROM `#__users`'
            . ' WHERE username=' . $username;

        //if an email
        if(strpos($username,'@')) {
            $query .= ' OR email='.$username;
        }

        $db->setQuery($query);

        $result = $db->loadObject();

        if($result)
        {
            $credentials['username'] = $result->username;
            $parts  = explode(':', $result->password);
            $crypt  = $parts[0];
            $salt   = isset($parts[1]) ? $parts[1] : '';
            $testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

            if($crypt === $testcrypt)
            {
                // Bring this in line with the rest of the system
                $user = JUser::getInstance($result->id);
                $response->username = $user->username;
                $response->email = $user->email;
                $response->fullname = $user->name;
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
