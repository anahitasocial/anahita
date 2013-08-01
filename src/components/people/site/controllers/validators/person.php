<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Person Validator
 *
 * @category   Anahita
 * @package    Com_People
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleControllerValidatorPerson extends LibBaseControllerValidatorDefault
{
    /**
     * Validates an email
     *
     * @param string $email   Email to validate
     * 
     * @return boolean
     */
    public function validateEmail($email)
    {
        $user = $this->getService('repos://site/users.user')->find(array('email'=>$email));
        
        if ( $user && $user->id != JFactory::getUser()->id ) {
            $this->setMessage('Email is already in use');
            return false;
        }
    
        return true;
    }
    
    /**
     * Validates a username
     *
     * @param string $email   Email to validate     * 
     *
     * @return boolean
     */
    public function validateUsername($username)
    {
        $user = $this->getService('repos://site/users.user')->find(array('username'=>$username));
        
        if ( $user && $user->id != JFactory::getUser()->id ) {
            $this->setMessage('Username is already in use');
            return false;
        }
    
        return true;
    }
}