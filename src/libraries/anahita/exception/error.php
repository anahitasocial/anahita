<?php
/**
 * @package     Anahita_Exception
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
class AnExceptionError extends ErrorException implements AnExceptionInterface
{
    /**
     * Constructor
     *
     * @param string  The exception message
     * @param integer The exception code
     */
    public function __construct($message, $code, $severity, $filename, $lineno)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }

        parent::__construct($message, $code, $severity, $filename, $lineno);
    }

    /**
     * Format the exception for display
     *
     * @return string
     */
    public function __toString()
    {
        return "exception '".get_class($this) ."' with message '".$this->getMessage()."' in ".$this->getFile().":".$this->getLine()
                ."\nStack trace:\n"
                . "  " . str_replace("\n", "\n  ", $this->getTraceAsString());
    }
}
