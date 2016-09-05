<?php

require_once JPATH_VENDOR.'/swiftmailer/swiftmailer/lib/swift_required.php';

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2016 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibMail extends KObject implements KServiceInstantiatable
{
    /**
    *   Mail sender
    *   @param array($name, $email)
    */
    protected $_sender = array();

    /**
    *   Mail body
    *
    *   @param string
    */
    protected $_body = null;

    /**
    *   List of recipients
    *
    *   @param array(array($name, $email), array($name, $email), ...)
    */
    protected $_recipients = array();

    /**
    *   List of carbon copy recipients
    *
    *   @param array(array($name, $email), array($name, $email), ...)
    */
    protected $_cc = array();

    /**
    *   List of blind carbon copy recipients
    *
    *   @param array(array($name, $email), array($name, $email), ...)
    */
    protected $_bcc = array();

    /**
    *   List of attachments
    *
    *   @param array of strings
    */
    protected $_attachments = array();

    /**
    *   Reply to email address
    *
    *   @param string email address
    */
    protected $_replyTo = null;

    /**
    *   Mail protocol
    *
    *   @param string 'smtp' or 'sendmail'
    */
    protected $_protocol = null;

    /**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct(KConfig $config = null)
	{
        parent::__construct($config);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'charset' => 'utf-8',
        ));

        parent::_initialize($config);
    }

    public function setSender($from)
    {

    }

    public function setBody($body)
    {

    }

    public function setRecipient($recipient)
    {

    }

    public function addCc($cc, $name = '')
    {

    }

    public function addBCc($bcc, $name = '')
    {

    }

    public function addAtachment($attachment, $name, $encoding, $type)
    {

    }

    public function addReplyTo($replyTo, $name = '')
    {

    }

    protected function useSendmail($sendmail)
    {

    }

    protected function useSmtp($auth, $host, $user, $pass, $secure, $port)
    {

    }

    public function send()
    {

    }
}
