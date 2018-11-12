<?php
/**
 * @package     Anahita_Exception
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnException extends Exception implements AnExceptionInterface
{
    /**
     * Previous exception if nested exception
     *
     * @var Exception
     */
    private $_previous = null;

    /**
     * Constructor
     *
     * @param string  The exception message
     * @param integer The exception code
     * @param object  The previous exception
     */
    public function __construct($message = null, $code = AnHttpResponse::INTERNAL_SERVER_ERROR, Exception $previous = null)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }

        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            parent::__construct($message, (int) $code);
            $this->_previous = $previous;
        } else {
            parent::__construct($message, (int) $code, $previous);
        }
    }

    /**
     * Overloading
     *
     * For PHP < 5.3.0, provides access to the getPrevious() method.
     *
     * @param  string   The function name
     * @param  array    The function arguments
     * @return mixed
     */
    public function __call($method, array $args)
    {
        if ('getprevious' == strtolower($method)) {
            return $this->_getPrevious();
        }

        return null;
    }

    /**
     * Get the previous Exception
     *
     * @return Exception
     */
    protected function _getPrevious()
    {
        return $this->_previous;
    }

    /**
     * Format the exception for display
     *
     * @return string
     */
    public function __toString()
    {
        return "Exception '".get_class($this) ."' with message '".$this->getMessage()."' in ".$this->getFile().":".$this->getLine();
    }
}
