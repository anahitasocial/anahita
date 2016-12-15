<?php

require_once ANPATH_VENDOR.'/swiftmailer/swiftmailer/lib/swift_required.php';

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
class AnMail extends KObject implements KServiceInstantiatable
{

    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_LOW = 4;
    const PRIORITY_LOWEST = 5;

    /**
    *   Mail sender address
    *
    *   @var array($email => $name)
    */
    protected $_sender = array();

    /**
    *   Mail from address
    *
    *   @var array($email => $name)
    */
    protected $_from = array();

    /**
    *   Mail to address
    *
    *   @var array($email => $name)
    */
    protected $_to = array();

    /**
    *   Mail subject
    *
    *   @var string
    */
    protected $_subject = null;

    /**
    *   Mail body
    *
    *   @var string
    */
    protected $_body = null;

    /**
    *   List of carbon copy recipients
    *
    *   @var array($email, array($email => $name), ...)
    */
    protected $_cc = array();

    /**
    *   List of blind carbon copy recipients
    *
    *   @var array($email, array($email => $name), ...)
    */
    protected $_bcc = array();

    /**
    *   Reply to email address
    *
    *   @var array($email, $name)
    */
    protected $_reply_to = array();

    /**
    *   Mailer
    *
    *   @var string 'mail', 'sendmail', 'smtp'
    */
    protected $_mailer = null;

    /**
    *   Mail Charset
    *
    *   @var string 'utf-8' by default
    */
    protected $_charset = null;

    /**
    *   Mail Maximum Line Length
    *
    *   @var int <= 1000
    */
    protected $_maxLineLength = null;

    /**
    *   Mail Content Type
    *
    *   @var string 'text/html' OR  'text/plain'
    */
    protected $_contentType = null;

    /**
    *   Mail Transport type
    *
    *   @var string 'sendmail' OR  'mail' OR 'smtp'
    */
    protected $_transport = null;

    /**
    * site settings object
    *
    * @var object
    */
    protected $_site_settings = null;

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

        $this->_charset = $config->charset;
        $this->_maxLineLength = $config->maxLineLength;
        $this->_priority = $config->priority;
        $this->_contentType = $config->contentType;
        $this->_site_settings = $config->site_settings;
        $this->_mailer = $config->mailer;
        $this->_transport = $this->_getTransport();
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
        $settings = $this->getService('com:settings.setting');

        $config->append(array(
    		'charset' => 'utf-8',
            'maxLineLength' => 900,
            'priority' => self::PRIORITY_NORMAL,
            'contentType' => 'text/html',
            'site_settings' => $settings
        ))->append(array(
            'mailer' => $settings->mailer
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $instance = new AnMail($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }


    public function reset()
    {
        $this->_sender = array();
        $this->_from = array();
        $this->_to = array();
        $this->_subject = '';
        $this->_body = '';
        $this->_cc = array();
        $this->_bcc = array();

        return $this;
    }

    /**
    * Set message priority
    *
    * @param int values 1 (highest) to 5 (lowest)
    * @return this
    */
    public function setPriority($level = self::PRIORITY_NORMAL)
    {
        $this->_priority = (int) $level;
        return $this;
    }

    /**
    * Set message subject
    *
    * @param string
    * @return this
    */
    public function setSubject($subject)
    {
        $this->_subject = trim($subject);
        return $this;
    }

    /**
    * Set message body
    *
    * @param string
    * @return this
    */
    public function setBody($body)
    {
        $this->_body = trim($body);
        return $this;
    }

    /**
    * Set Sender address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function setSender($email, $name = '')
    {
        $this->_addAddress($email, $name, 'sender');
        return $this;
    }

    /**
    * Set From address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function setFrom($email, $name = '')
    {
        $this->_addAddress($email, $name, 'from');
        return $this;
    }

    /**
    * Set To address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function setTo($email, $name = '')
    {
        $this->_addAddress($email, $name, 'to');
        return $this;
    }

    /**
    * Add a carbon copy address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function addCc($email, $name = '')
    {
        $this->_addAddress($email, $name, 'cc');
        return $this;
    }

    /**
    * Add a blind carbon copy address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function addBCc($email, $name = '')
    {
        $this->_addAddress($email, $name, 'bcc');
        return $this;
    }

    /**
    * Add a reply to address
    *
    * @param string email address format
    * @param string sender's name
    * @return this
    */
    public function setReplyTo($email, $name = '')
    {
        $this->_addAddress($email, $name, 'reply_to');
        return $this;
    }

    /**
    * Adds an address
    *
    * @param string email address format
    * @param string sender's name
    * @param string 'recipient' OR 'cc' OR 'bcc' OR 'replyTo'
    *
    * @return void
    */
    protected function _addAddress($email, $name, $type) {

        $type = '_'.$type;

        $allowed = array(
            '_sender',
            '_from',
            '_to',
            '_cc',
            '_bcc',
            '_reply_to'
        );

        if (in_array($type, $allowed)) {
            if ($name != '') {
                $this->{$type}[$email] = $name;
            } else {
                $this->{$type}[] = $email;
            }
        }
    }

    /**
    * Obtains the swift mailer transport object
    *
    * @return object Swift_SmtpTransport instance
    */
    protected function _getTransport()
    {
        if ($this->_mailer === 'smtp') {
            $transport = Swift_SmtpTransport::newInstance($this->_site_settings->smtphost, $this->_site_settings->smtpport)
            ->setUsername($this->_site_settings->smtpuser)
            ->setPassword($this->_site_settings->smtppass);
        } elseif ($this->_mailer === 'sendmail') {
            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs');
        } else {
            $transport = Swift_MailTransport::newInstance();
        }

        return $transport;
    }

    /**
    * Creates a message to be sent out
    *
    * @return object Swift_Message instance
    */
    protected function _createMessage()
    {
        $message = Swift_Message::newInstance()
        ->setCharset($this->_charset)
        ->setContentType($this->_contentType)
        ->setPriority($this->_priority)
        ->setMaxLineLength($this->_maxLineLength)
        ->setSubject($this->_subject)
        ->setTo($this->_to)
        ->setBody($this->_body);

        if(count($this->_cc)) {
            $message->setCc($this->_cc);
        }

        if(count($this->_bcc)) {
            $message->setBcc($this->_bcc);
        }

        if(count($this->_sender)) {
            $message->setSender($this->_sender);
        } else {
            $message->setSender($this->_site_settings->mailfrom, $this->_site_settings->fromname);
        }

        if(count($this->_from)) {
            $message->setFrom($this->_from);
        } else {
            $message->setFrom($this->_site_settings->mailfrom, $this->_site_settings->fromname);
        }

        if(count($this->_reply_to)) {
            $message->setReplyTo($this->_reply_to);
        } else {
            $message->setReplyTo($this->_site_settings->mailfrom, $this->_site_settings->fromname);
        }

        return $message;
    }

    /**
    * Sends a message
    *
    * @return boolean
    */
    public function send()
    {
        $mailer = Swift_Mailer::newInstance($this->_transport);
        $message = $this->_createMessage();
        return (boolean) $mailer->send($message);
    }
}
