<?php

/**
 * Person Validator.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerValidatorPerson extends LibBaseControllerValidatorDefault
{
    /**
     * Validates an email.
     *
     * @param string $email Email to validate
     *
     * @return bool
     */
    public function validateEmail($email)
    {
        $person = $this->getService('repos:people.person')->find(array('email' => $email));

        if ($person) {
            $this->setMessage('Email is already in use');
            return false;
        }

        return true;
    }

    /**
     * Validates a username.
     *
     * @param string $email Email to validate     *
     *
     * @return bool
     */
    public function validateUsername($username)
    {
        $person = $this->getService('repos:people.person')->find(array('username' => $username));

        if ($person) {
            $this->setMessage('Username is already in use');
            return false;
        }

        return true;
    }
}
