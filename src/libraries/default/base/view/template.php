<?php

/**
 * Abstract Template View. Very similar to nooku except different in loading the template.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseViewTemplate extends LibBaseViewAbstract
{
    /**
     * Template identifier (APP::com.COMPONENT.template.NAME).
     *
     * @var string|object
     */
    protected $_template;

    /**
     * Callback for escaping.
     *
     * @var string
     */
    protected $_escape;

    /**
     * Auto assign.
     *
     * @var bool
     */
    protected $_auto_assign;

    /**
     * The uniform resource locator.
     *
     * @var object
     */
    protected $_mediaurl;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //set the media url
        if (!$config->media_url instanceof KHttpUrl) {
            $this->_mediaurl = KService::get('koowa:http.url', array('url' => $config->media_url));
        } else {
            $this->_mediaurl = $config->media_url;
        }

        // set the auto assign state
        $this->_auto_assign = $config->auto_assign;

         // user-defined escaping callback
        $this->setEscape($config->escape);

        // set the template object
        $this->_template = $config->template;

        //Set the template filters
        if (! empty($config->template_filters)) {
            $this->getTemplate()->addFilter($config->template_filters);
        }

        // Add default template paths
        $this->getTemplate()->addSearchPath(KConfig::unbox($config->template_paths));

        //Add alias filter for media:// namespace
        $this->getTemplate()->getFilter('alias')->append(
            array('media://' => (string) $this->_mediaurl.'/'), LibBaseTemplateFilter::MODE_READ | LibBaseTemplateFilter::MODE_WRITE
        );
    }

    /**
     * Initializes the config for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'escape' => 'htmlspecialchars',
            'template' => $this->getName(),
            'template_filters' => array(
                'shorttag', 
                'alias', 
                'variable',
            ),
            'template_paths' => array(),
            'auto_assign' => true,
            'media_url' => '/media',
        ));

        parent::_initialize($config);
    }

    /**
     * Escapes a value for output in a view script.
     *
     * @param mixed $var The output to escape.
     *
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        return call_user_func($this->_escape, $var);
    }

    /**
     * Sets the _escape() callback.
     *
     * @param   mixed The callback for _escape() to use.
     *
     * @return LibBaseViewAbstract
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
        return $this;
    }

    /**
     * Get the identifier for the template with the same name.
     *
     * @return KServiceIdentifierInterface
     */
    public function getTemplate()
    {
        if (! $this->_template instanceof LibBaseTemplateAbstract) {
            //Make sure we have a template identifier
            if (! ($this->_template instanceof KServiceIdentifier)) {
                $this->setTemplate($this->_template);
            }

            $config = array(
                'view' => $this
            );

            $this->_template = $this->getService($this->_template, $config);
        }

        return $this->_template;
    }

    /**
     * Method to set a template object attached to the view.
     *
     * @param   mixed   An object that implements KObjectIdentifiable, an object that
     *                  implements KIndentifierInterface or valid identifier string
     *
     * @throws KDatabaseRowsetException If the identifier is not a table identifier
     *
     * @return LibBaseViewAbstract
     */
    public function setTemplate($template)
    {
        if (! ($template instanceof LibBaseTemplateAbstract)) {
            if (is_string($template) && strpos($template, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('template');
                $identifier->name = $template;
            } else {
                $identifier = $this->getIdentifier($template);
            }

            if ($identifier->path[0] != 'template') {
                throw new KViewException('Identifier: '.$identifier.' is not a template identifier');
            }

            register_default(array('identifier' => $identifier, 'prefix' => $this, 'name' => array('Template'.ucfirst($this->getName()), 'TemplateDefault')));

            $template = $identifier;
        }

        $this->_template = $template;

        return $this;
    }

    /**
     * Calls the methods _beforeLayout($layout) and _layout[Layout] and runs the command
     * before.load if there are any commands in the queue. For any layout rendered through the view
     * the data of the view is passed to the template regardless. If $data has duplicate keys as the
     * $view->data then $data values replaces the values with duplicate keys.
     *
     * @param string $layout The layout
     * @param array  $data   The data
     */
    public function load($template, array $data = array())
    {
        if (method_exists($this, '_beforeLayout')) {
            $this->_beforeLayout($template);
        }

        $method = '_layout'.AnInflector::camelize($template);

        if (method_exists($this, $method)) {
            $this->$method();
        }

        $data = array_merge($this->_data, $data);
        $output = $this->getTemplate()->loadTemplate($template, $data)->render();

        return $output;
    }

    /**
     * Return the views output. If a $layout is passed, the layout will be used as identifier
     * to render. Before a layout is displayed, method $self::execute($layout) will be called.
     *
     * @return string The output of the view
     */
    public function display()
    {
        $this->output = '';
        $output = $this->load($this->_layout);
        $this->output = $this->output.$output;

        return $this->output;
    }

    /**
     * Get the view media url.
     *
     * @return object A KHttpUrl object
     */
    public function getMediaUrl()
    {
        return $this->_mediaurl;
    }
}
