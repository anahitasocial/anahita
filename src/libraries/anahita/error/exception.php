<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Anahita error exception handles multiple errors and exception objects.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnErrorException extends AnException
{
    /**
     * An array of Exceptions and Error object.
     *
     * @var Iterator
     */
    protected $_errors = array();

    /**
     * Constructor.
     *
     * @param array $config An optional AnConfig object with configuration options.
     */
    public function __construct($errors = array(), $code = AnHttpResponse::INTERNAL_SERVER_ERROR, Exception $previous = null)
    {
        $errors = (array) $errors;

        foreach ($errors as $error) {
            $this->_errors[] = $error;
        }

        $message = AnHttpResponse::getMessage($code);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Format the exception for display.
     *
     * @return string
     */
    public function __toString()
    {
        $errors = array();

        foreach ($this->_errors as $error) {
            $errors[] = $error->toArray();
        }

        $msg = "Exception '".get_class($this)."' with message '".$this->getMessage()."' in ".$this->getFile().':'.$this->getLine();
        $msg .= ' '.json_encode($errors);

        return $msg;
    }

    /**
     * Return an array of errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
