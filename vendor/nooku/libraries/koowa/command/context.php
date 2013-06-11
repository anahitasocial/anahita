<?php
/**
 * @version     $Id: interface.php 1366 2009-11-28 01:34:00Z johan $
 * @package     Koowa_Command
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Command Context
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Command
 */
class KCommandContext extends KConfig
{
    /**
     * Error
     *
     * @var string
     */
    protected $_error;
    
    /**
     * The command subject
     *
     * @var  object
     */
    protected $_subject;
    
    /**
     * Set the error
     *
     * @return  KCommandContext
     */
    public function setError($error) 
    {
        $this->_error = $error;
        return $this;
    }
    
    /**
     * Get the error
     *
     * @return  string  The error
     */
    public function getError() 
    {
        return $this->_error;
    }
    
    /**
    * Get the command subject 
    *     
    * @return object	The command subject
    */
    public function getSubject()
    {
        return $this->_subject;
    }
    
    /**
     * Set the command subject
     *
     * @param object	The command subject
     * @return KEvent
     */
    public function setSubject(KObjectServiceable $subject)
    {
        $this->_subject = $publisher;
        return $this;
    }
}
