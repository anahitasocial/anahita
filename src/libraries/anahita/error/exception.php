<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Anahita error exception handles multiple errors and exception objects.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnErrorException extends KException
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
     * @param array $config An optional KConfig object with configuration options.
     */
    public function __construct($errors = array(), $code = KHttpResponse::INTERNAL_SERVER_ERROR, Exception $previous = null)
    {
        $errors = (array) $errors;

        foreach ($errors as $error) {
            $this->_errors[] = $error;
        }

        $message = KHttpResponse::getMessage($code);

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
