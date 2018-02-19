<?php

/**
 * Rendering script.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationTemplateHelperRender extends LibBaseTemplateHelperAbstract
{
    /**
     * Template parameters.
     *
     * @return KConfig
     */
    protected $_params = null;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_params = $this->_template->getView()->getParams();
    }

    /**
     * Renders the logo hyperlinked.
     *
     * @param $config Configuration
     *
     * @return string
     */
    public function logo($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'show_logo' => pick($this->_params->showLogo, 1),
            'name' => pick($this->_params->brandName, 'Anahita'),
            'url' => 'base://',
        ));

        $showLogo = ($config->show_logo) ? ' brand-logo' : '';

        return '<a class="brand'.$showLogo.'" href="'.$config->url.'">'.$config->name.'</a>';
    }

    /**
     * Renders the favicon tag.
     *
     * @param $config Configuration
     *
     * @return string
     */
    public function favicon($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'favicon' => pick($this->_params->favicon, 'favicon.ico'),
            'type' => 'image/png',
            'style' => pick($this->_params->cssStyle, 'style1'),
            'url' => 'base://',
        ));

        $paths = array(
            ANPATH_THEMES.DS.'base'.DS.'css'.DS.'images',
            ANPATH_THEMES.DS.$this->getIdentifier()->package.DS.'css'.DS.$config->style.DS.'images',
        );

        $finder = $this->getService('anahita:file.pathfinder');

        $finder->addSearchDirs($paths);

        $path = str_replace('\\', '/', str_replace(ANPATH_ROOT.DS, 'base://', $finder->getPath('favicon.ico')));

        return '<link rel="icon" type="'.$config->type.'" href="'.$path.'" />';
    }

    /**
     * Renders the template style.
     *
     * @param array $config Configuration
     *
     * @return string
     */
    public function style($config = array())
    {
        require_once 'less/compiler.php';

        $config = new KConfig($config);

        $config->append(array(
            'parse_urls' => true,
            'style' => pick($this->_params->cssStyle, 'style1'),
            'compile' => pick($this->_params->compilestyle, 0),
            'compress' => pick($this->_params->compresstyle, 1),
        ));

        $paths = array(
            ANPATH_ROOT.DS.'media'.DS.'lib_anahita'.DS.'css',
            ANPATH_THEMES.DS.'base'.DS.'css',
            $css_folder = ANPATH_ROOT.DS.'templates'.DS.$this->getIdentifier()->package.DS.'css'.DS.$config->style,
        );

        $finder = $this->getService('anahita:file.pathfinder');
        $finder->addSearchDirs($paths);
        $style = $finder->getPath('style.less');
        $css = $css_folder.DS.'style.css';

        //compile
        if ($config->compile > 0 && !empty($style)) {
            $this->_template->renderHelper('less.compile', array(
                'force' => $config->compile > 1,
                'compress' => $config->compress,
                'parse_urls' => $config->parse_urls,
                'import' => $finder->getSearchDirs(),
                'input' => $style,
                'output' => $css,
            ));
        }

        $cssHref = str_replace('\\', '/', str_replace(ANPATH_ROOT.DS, 'base://', $css));

        return '<link rel="stylesheet" href="'.$cssHref.'" type="text/css" />';
    }

    /**
     * Render the document queued messages.
     *
     * @return string
     */
    public function messages()
    {
        $session = KService::get('com:sessions');
        $queue = (array) $session->get('controller.queue', array());

        $session->set('controller.queue', null);

        if (isset($queue['message'])) {
            $message = $queue['message'];
            $config = array('closable' => true);

            if (isset($message['type'])) {
                $config['type'] = $message['type'];
            }

            return $this->getTemplate()->renderHelper('ui.message', $message['message'], $config);
        }

        return '';
    }
}
