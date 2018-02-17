<?php

/**
 * Mailer Behavior can be used to send emails using a template.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMailerControllerBehaviorMailer extends AnControllerBehaviorAbstract
{
    /**
     * Email View.
     *
     * @var ComMailerEmailView
     */
    protected $_template_view;

    /**
     * Mailer test options.
     *
     * @var KConfig
     */
    protected $_test_options;

    /**
     * Base URL to use within the mails.
     *
     * @var KHttpUrl
     */
    protected $_base_url;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_template_view = $config->template_view;
        $this->_base_url = $config->base_url;
        $this->_test_options = $config->test_options;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $base_url = KRequest::base();
        $settings = $this->getService('com:settings.setting');

        if (PHP_SAPI == 'cli' && $settings->live_site != '') {
            $scheme = is_ssl() ? 'https://' : 'http://';
            $base_url = $scheme.$settings->live_site;
        }

        $config->append(array(
            'base_url' => $base_url,
            'test_options' => array(
                'enabled' => get_config_value('mailer.debug', false),
                'email' => get_config_value('mailer.redirect_email'),
                'log' => $settings->tmp_path . '/emails/',
            ),
            'template_view' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the mailer test options.
     *
     * @return array
     */
    public function getTestOptions()
    {
        return $this->_test_options;
    }

    /**
     * Return the email view.
     *
     * @return ComMailerViewTemplate
     */
    public function getEmailTemplateView()
    {
        if (! $this->_template_view instanceof LibBaseViewTemplate) {

            if (!isset($this->_template_view)) {
                $this->_template_view = clone $this->_mixer->getIdentifier();
                $this->_template_view->path = array('emails');
                $this->_template_view->name = 'template';
            }

            $identifier = clone $this->_mixer->getIdentifier();
            $identifier->path = array('emails');

            $directory = dirname($identifier->filepath);
            if (strpos($directory, ANPATH_ROOT) === false) {
                $directory = ANPATH_ROOT.$directory;
            }

            $paths[] = $directory;
            $paths[] = implode(DS, array(ANPATH_THEMES, $this->getService('application')->getTemplate(), 'emails', $identifier->type.'_'.$identifier->package));
            $paths[] = implode(DS, array(ANPATH_THEMES, $this->getService('application')->getTemplate(), 'emails'));

            $config = array(
                'base_url' => $this->_base_url,
                'template_paths' => $paths,
            );

            register_default(array(
                'identifier' => $this->_template_view,
                'default' => 'LibBaseViewTemplate'
            ));

            $this->_template_view = $this->getService($this->_template_view, $config);
        }

        return $this->_template_view;
    }

    /**
     * Retun the mail into a string.
     *
     * @return string
     */
    public function renderMail($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'layout' => 'default',
        ));

        $layout = $config->layout;
        $data = $this->getState()->toArray();

        if ($this->getState()->getItem()) {
            $data[$this->_mixer->getIdentifier()->name] = $this->getState()->getItem();
        }

        if ($this->getState()->getList()) {
            $data[AnInflector::pluralize($this->_mixer->getIdentifier()->name)] = $this->getState()->getList();
        }

        $config->append(array(
            'data' => $data
        ));

        $template = $this->getEmailTemplateView()->getTemplate();
        $data = array_merge($config['data'], array('config' => $config));

        if ($layout && $template->findTemplate($layout)) {
            $output = $template->loadTemplate($layout, array('output' => $output))->render();
        } else {
            $output = $template->loadTemplate($config->template, $data)->render();
        }

        return $output;
    }

    /**
     * Replaces to with the admin emails.
     *
     * @param array('template' => '...', 'subject' => '...')
     *
     * @see ComMailerControllerBehaviorMaile::mail
     */
    public function mailAdmins($config)
    {
        $admins = $this->getService('repos:people.person')
                       ->fetchSet(array(
                           'usertype' => ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR
                       ));

        $mails = array();

        foreach($admins as $admin) {
           $mails[] = array(
               'subject' => $config['subject'],
               'template' => $config['template'],
               'to' => $admin->email
           );
        }

        return $this->mail($mails);
    }

    /**
    * Send an email.
    *
    * @param array $mails An array of config
    *        'to' => array of recipients
    *        'template'=> name of the email template to use
    *        'layout' => the email layout. It's set to default
    *        'data' => array of data
    *        'subject' => the mail subject
    */
    public function mail($mails)
    {
        $mailer = $this->getService('anahita:mail');

        foreach($mails as $mail) {
            $to = ($this->_test_options->enabled) ? $this->_test_options->email : $mail['to'];
            $subject = KService::get('koowa:filter.string')->sanitize($mail['subject']);

            if (isset($mail['body'])) {
                $body = $mail['body'];
            } else {
                $body = $this->renderMail($mail);
            }

            $mailer->reset()
            ->setSubject($subject)
            ->setBody($body)
            ->setTo($to)
            ->send();

            if ($this->_test_options->enabled && $this->_test_options->log) {

                $subject = KService::get('koowa:filter.cmd')->sanitize(str_replace(' ', '_', $subject));
                $file = $this->_test_options->log.'/'.$subject.'.'.time().'.html';

                if (!file_exists(dirname($file))) {
                    mkdir(dirname($file), 0755);
                }

                file_put_contents($file, $body);
            }
        }
    }
}
