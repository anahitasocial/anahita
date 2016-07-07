<?php

/**
 * JSite application. Temporary until merged with the dispatcher.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class JSite extends JApplication
{
    /**
     * Template.
     *
     * @var string
     */
    protected $_template;

    /**
     * Application Router.
     *
     * @var JRouter
     */
    protected $_router;

    /**
     * Initialise the application.
     *
     * @param array $options Initialization options
     */
    public function initialise($options = array())
    {
        $setting = new JConfig();
        $options['language'] = $setting->language;

        // One last check to make sure we have something
        if (!JLanguage::exists($options['language'])) {
            $options['language'] = 'en-GB';
        }

        parent::initialise($options);
    }

    /**
     * Get the template.
     *
     * @return string The template name
     */
    public function getTemplate()
    {
        if (!isset($this->_template)) {
            if (!KService::get('application.registry')->offsetExists('application-template')) {

                $settings = new JConfig();
                $template = (isset($settings->template)) ? $settings->template : 'shiraz';

                KService::get('application.registry')->offsetSet('application-template', $template);
            }

            $template = KService::get('application.registry')->offsetGet('application-template');
            $this->setTemplate(pick($template, 'base'));
        }

        return $this->_template;
    }

    /**
     * Overrides the default template that would be used.
     *
     * @param string $template The template name
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**
     * Set the application router.
     *
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->_router = $router;

        return $this;
    }

    /**
     * Return a reference to the JRouter object.
     *
     * @return JRouter
     */
    public function &getRouter($name = null, $options = array())
    {
        if (!isset($this->_router)) {
            $this->_router = KService::get('com://site/application.router', array('enable_rewrite' => JFactory::getConfig()->getValue('sef_rewrite')));
        }

        return $this->_router;
    }
}
