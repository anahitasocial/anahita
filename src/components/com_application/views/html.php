<?php

/**
 * Page Controller.
 *
 * @category   Anahita
 * @package    com_application
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2017 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       https://www.GetAnahita.com
 */
class ComApplicationViewHtml extends LibBaseViewTemplate
{
    /**
     * Page content to display.
     *
     * @return KException|string
     */
    public $content = "";

    /**
     * Template Parameters.
     *
     * KConfig
     */
    protected $_params = null;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        $config->media_url = 'base://media';
        $config->base_url = $config->service_container->get('application')->getRouter()->getBaseUrl();

        parent::__construct($config);

        $this->_params = new KConfig();
        $this->setParams($config->params);
        $this->getService('anahita:language')->load('tpl_'.$this->getIdentifier()->package);
        $this->getTemplate()->getFilter('alias')
             ->append(array('@render(\'' => '$this->renderHelper(\'render.'))
             ->append(array('base://' => $this->getBaseUrl().'/'), LibBaseTemplateFilter::MODE_WRITE);
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
        $identifier = clone $this->getIdentifier();

        $identifier->path = array();

        $paths[] = ANPATH_THEMES.DS.'base'.DS.$this->getFormat();
        $paths[] = ANPATH_BASE.dirname($identifier->filepath).DS.$this->getFormat();

        $config->append(array(
            'template_paths' => $paths,
        ));

        $params = '';

        if (is_readable($file = ANPATH_THEMES.DS.$this->getIdentifier()->package.DS.'params.ini')) {
            $params = parse_ini_file($file);
        }

        $config->append(array(
            'mimetype' => 'text/html',
            'params' => $params,
            'template_filters' => array('shorttag', 'html', 'alias'),
        ));

        parent::_initialize($config);
    }

    /**
     * Displays the template.
     *
     * @return string
     */
    public function display()
    {
        if ($this->content instanceof Exception) {

            $error = $this->content;
            $layout = $error->getCode();

            if (! $this->getTemplate()->findPath('errors'.DS.$layout.'.php')) {
                $layout = 'default';
            }

            $this->content = $this->getTemplate()->loadTemplate('errors'.DS.$layout, array('error' => $error))->render();

            $settings = $this->getService('com:settings.setting');

            if ($settings->debug) {

                $traces = array();
                $traces[] = '<h4>Exception '.get_class($error).' with message "'.$error->getMessage().'"</h4>';
                $traces[] = $error->getFile().':'.$error->getLine();

                foreach ($error->getTrace() as $trace) {
                    $str = '';

                    if (isset($trace['file'])) {
                        $str = $trace['file'].':';
                    }

                    if (isset($trace['line'])) {
                        $str .= $trace['line'];
                    }

                    if (empty($str)) {
                        continue;
                    }

                    $traces[] = $str;
                }

                $this->content .= '<pre>'.implode('<br />', $traces).'</pre>';
            }
        }

        $this->output = $this->getTemplate()->loadTemplate($this->getLayout(), array('output' => $this->content))->render();

        return $this->output;
    }

    /**
     * Set the template parameters.
     *
     * @param array $params
     */
    public function setParams($params)
    {
        $this->_params->append($params);
    }

    /**
     * Get template parameters.
     *
     * @return KConfig
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * (non-PHPdoc).
     *
     * @see LibBaseViewAbstract::getRoute()
     */
    public function getRoute($route = '', $fqr = true)
    {
        if (strpos($route, 'index.php?') === false) {
            $route .= 'index.php?'.$route;
        }

        return $this->getService('com:application')->getRouter()->build($route, $fqr);
    }
}
