<?php

/**
 * JRouter application. Temporary until merged with the KDispatcherRouter.
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
class ComApplicationRouter extends KObject
{
    /**
     * cloneable url.
     *
     * @var KHttpUrl
     */
    protected $_clonable_url = null;

    /**
     * If enabled then index.php is removed from the routes.
     *
     * @var bool
     */
    protected $_enable_rewrite = false;

    /**
     * base url.
     *
     * @var KHttpUrl
     */
    protected $_base_url = null;

    /**
     * Component routers.
     *
     * @var array
     */
    protected $_routers = array();

    /**
     * Constructor.
     *
     * @param array $config An optional KConfig object with configuration options.
     */
    public function __construct($config = array())
    {
        $config = new KConfig($config);

        parent::__construct($config);

        $this->_enable_rewrite = $config->enable_rewrite;
        $this->_base_url = $config->base_url;

        if (is_string($this->_base_url)) {
            $this->_base_url = $this->getService('koowa:http.url', array('url' => $this->_base_url));
        }

        $this->_clonable_url = $config->url;
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
        $base = clone KRequest::base();

        foreach (array('host', 'scheme', 'port', 'user', 'pass') as $part) {
            $base->$part = KRequest::url()->$part;
        }

        if (PHP_SAPI == 'cli') {
            $base->scheme = extension_loaded('openssl') ? 'https' : 'http';
            $settings = $this->getService('com:settings.setting');
            $base->host = KRequest::get('server.HOSTNAME', 'url', $settings->live_site);
            $base->path = '';
        }

        $config->append(array(
            'base_url' => $base,
            'enable_rewrite' => false,
            'url' => clone KService::get('koowa:http.url')
        ));

        parent::_initialize($config);
    }

    /**
     * Return if rewerite is enabled.
     *
     * @return bool
     */
    public function rewriteEnabled()
    {
        return $this->_enable_rewrite;
    }

    /**
     * Return the base url.
     *
     * @return KHttpUrl
     */
    public function getBaseUrl()
    {
        return $this->_base_url;
    }

    /**
     * Parses the URI.
     *
     * @param $uri
     */
    public function parse($url)
    {
        $this->_fixUrlForParsing($url);

        if (empty($url->path) && !isset($url->query['option'])) {
            $url->path = (get_viewer()->guest()) ? 'pages' : 'dashboard';
        }

        $segments = explode('/', trim($url->path, '/'));
        $segments = array_filter($segments);

        if (count($segments)) {
            $component = array_shift($segments);
            $url->query = array_merge(array('option' => 'com_'.$component), $url->query);
            $component = str_replace('com_', '', $url->query['option']);
            $query = $this->getComponentRouter($component)->parse($segments);
            $url->query = array_merge($url->query, array('option' => 'com_'.$component), $query);
        }

        return $url;
    }

    /**
     * Builds a SEF URL.
     *
     * @param string $url URL to build
     * @param bool   $fqr Full query resolution
     */
    public function build(&$url = '', $fqr = true)
    {
        if (is_array($url)) {
            $url = '?'.http_build_query($url, '', '&');
        }

        $url = (string) $url;

        //remove the index.php for urls that starts with index.php?
        if (strpos($url, 'index.php?') === 0) {
            $url = substr($url, 9);
        }
        //add ? to the urls that starts with a query key=
        //elseif ( preg_match('/^\w+=/', $url) ) {
        elseif (preg_match('%^[^?/]+=%', $url)) {
            $url = '?'.$url;
        }

        $uri = clone $this->_clonable_url;

        $uri->setUrl($url);

        if ($uri->scheme || $uri->path) {
            return $uri;
        }

        $query = $uri->query;

        if (isset($query['format'])) {
            $uri->format = $query['format'];
            unset($query['format']);
        }

        if ($uri->format == 'html') {
            $uri->format = null;
        }

        $parts = array();

        if (isset($query['option'])) {
            $router = $this->getComponentRouter(str_replace('com_', '', $query['option']));
            $parts = pick($router->build($query), array());
        }

        if (isset($query['option'])) {
            array_unshift($parts, str_replace('com_', '', $query['option']));
            unset($query['option']);
        }

        $uri->query = $query;

        if (!$this->_enable_rewrite) {
            array_unshift($parts, 'index.php');
        } elseif (empty($parts)) {
            array_unshift($parts, '');
        }

        array_unshift($parts, $this->_base_url->path);

        $path = implode('/', $parts);

        $uri->path = $path;

        if ($fqr) {
            foreach (array('host', 'scheme', 'port', 'user', 'pass') as $part) {
                $uri->$part = $this->_base_url->$part;
            }
        }

        return $uri;
    }

    /**
     * Returna component router.
     *
     * @param string $component Component name
     *
     * @return ComBaseRouter
     */
    public function getComponentRouter($component)
    {
        if (! isset($this->_routers[$component])) {
            $identifier = clone $this->getIdentifier();
            $identifier->path = array();
            $identifier->package = str_replace('com_', '', $component);
            $identifier->name = 'router';

            $this->_routers[$component] = $this->getService($identifier);
        }

        return $this->_routers[$component];
    }

    /**
     * Fixes the url path.
     *
     * @param KHttpUrl $url
     */
    protected function _fixUrlForParsing($url)
    {
        $path = $url->path;

        //bug in request
        if ($url->format == 'php') {
            $path .= '.php';
            $url->format = null;
        }

        $path = substr_replace($path, '', 0, strlen($this->_base_url->path));
        $path = preg_replace('/index\/?.php/', '', $path);
        $path = trim($path, '/');

        $url->path = $path;
        $url->format = $url->format ? $url->format : pick(KRequest::format(), 'html');

        if (! empty($url->format)) {
            $url->query['format'] = $url->format;
        }
    }
}
