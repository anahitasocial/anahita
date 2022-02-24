<?php
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2022 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnMail extends AnObject implements AnServiceInstantiatable
{

    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_LOW = 4;
    const PRIORITY_LOWEST = 5;
    
    const CONTENT_TYPE = 'text/html';
    
    const MAILER_SMTP = 'smtp';
    const MAILER_SENDMAIL = 'sendmail';

    /**
    *   Mail sender address
    *
    *   @var Address
    */
    protected $_sender = null;

    /**
    *   Mail from address
    *
    *   @var Address
    */
    protected $_from = null;

    /**
    *   Mail to address
    *
    *   @var array(Address)
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
    *   @var array(Address)
    */
    protected $_cc = array();

    /**
    *   List of blind carbon copy recipients
    *
    *   @var array(Address)
    */
    protected $_bcc = array();

    /**
    *   Reply to email address
    *
    *   @var array(Address)
    */
    protected $_reply_to = array();

    /**
    *   Mailer
    *
    *   @var string 'mail', 'sendmail', 'smtp'
    */
    protected $_mailer = null;

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
	 * @param 	object 	An optional AnConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct(AnConfig $config = null)
	{
        parent::__construct($config);

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
     * @param 	object 	An optional AnConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(AnConfig $config)
    {
        $settings = $this->getService('com:settings.config');

        $config->append(array(
            'priority' => self::PRIORITY_NORMAL,
            'contentType' => self::CONTENT_TYPE,
            'site_settings' => $settings
        ))->append(array(
            'mailer' => $settings->mailer
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $instance = new AnMail($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }


    public function reset()
    {
        $this->_sender = null;
        $this->_from = null;
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
        $this->_sender = new Address($email, $name);
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
        $this->_from = new Address($email, $name);
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
        $this->_to[] = new Address($email, $name);
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
        $this->_cc[] = new Address($email, $name);
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
        $this->_bcc[] = new Address($email, $name);
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
        $this->_reply_to = new Address($email, $name);
        return $this;
    }

    /**
    * Obtains the swift mailer transport object
    *
    * @return object Swift_SmtpTransport instance
    */
    protected function _getTransport()
    {        
        if ($this->_mailer === self::MAILER_SMTP) {
            $dsn = 'smtp://' . $this->_site_settings->smtp_user . ':' . $this->_site_settings->smtp_pass . '@' . $this->_site_settings->smtp_host . ':' . $this->_site_settings->smtp_port;
        } elseif ($this->_mailer === self::MAILER_SENDMAIL) {
            $dsn = 'sendmail://default';
        } else {
            $dsn = 'native://default';
        } 
        
        $transport = Transport::fromDsn($dsn);

        return $transport;
    }

    /**
    * Creates a message to be sent out
    *
    * @return object Swift_Message instance
    */
    protected function _createMessage()
    {
        $message = (new Email())
        ->priority($this->_priority)
        ->subject($this->_subject)
        ->to(...$this->_to)
        ->html($this->_body);
        
        if ($this->_contentType == self::CONTENT_TYPE) {
            $message->html($this->_body);
        } else {
            $message->text($this->_body);
        }

        if(! empty($this->_cc)) {
            $message->cc(...$this->_cc);
        }

        if(! empty($this->_bcc)) {
            $message->bcc(...$this->_bcc);
        }

        if(! empty($this->_sender)) {
            $message->sender($this->_sender);
        } else {
            $message->sender(new Address(
                $this->_site_settings->mailfrom, 
                $this->_site_settings->fromname
            ));
        }

        if(! empty($this->_from)) {
            $message->from($this->_from);
        } else {
            $message->from(new Address(
                $this->_site_settings->mailfrom, 
                $this->_site_settings->fromname
            ));
        }

        if(count($this->_reply_to)) {
            $message->replyTo($this->_reply_to);
        } else {
            $message->replyTo(new Address(
                $this->_site_settings->mailfrom, 
                $this->_site_settings->fromname));
        }

        return $message;
    }

    /**
    * Sends a message
    *
    * @return void
    */
    public function send()
    {
        $mailer = new Mailer($this->_transport);
        $email = $this->_createMessage();
        
        return $mailer->send($email);
    }
}
