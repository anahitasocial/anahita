<?php

 /**
 * Command Context
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @package     AnCommand
 * @link        https://www.Anahita.io
 */
class AnCommandContext extends AnConfig
{
    /**
     * Error
     *
     * @var string
     */
    protected $_error = '';
    
    /**
     * The command subject
     *
     * @var  object
     */
    protected $_subject = null;
    
    /**
     * Set the error
     *
     * @return  AnCommandContext
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
     * @return AnEvent
     */
    public function setSubject(AnObjectServiceable $subject)
    {
        $this->_subject = $publisher;
        return $this;
    }
}
