<?php

/**
 * Content Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2016 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPagesControllerPage extends ComBaseControllerResource
{
    /**
     * Content base path.
     *
     * @var string
     */
    protected $_base_path;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_base_path = $config->base_path;

        $this->setView($config->view);
        $this->getService()->set('com:html.controller', $this);

        if ($this->_base_path) {
            if (!preg_match('%^(\w:)?[/\\\\]%', $this->_base_path)) {
                $this->_base_path = realpath(ANPATH_ROOT.'/'.$this->_base_path);
            }

            $this->getService()->setConfig($this->_view, array(
                    'template_paths' => $this->_base_path,
            ));
        }
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'base_path' => get_config_value('com_pages.content_path'),
            'request' => array('layout' => 'default'),
        ));

        parent::_initialize($config);
    }
}
