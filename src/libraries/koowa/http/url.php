<?php
/**
 * @version     $Id: url.php 4687 2012-06-04 19:50:30Z johanjanssens $
 * @package     Koowa_Http
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * HTTP Url Class
 *
 * This class helps you to create and manipulate urls, including query
 * strings and path elements. It does so by splitting up the pieces of the
 * url and allowing you modify them individually; you can then then fetch
 * them as a single url string. This helps when building complex links,
 * such as in a paged navigation system.
 *
 * The following is a simple example. Say that the page address is currently
 * `http://anonymous::guest@example.com/path/to/index.php/foo/bar?baz=dib#anchor`.
 *
 * You can use KHttpUrl to parse this complex string very easily:
 *
 * <code>
 * <?php
 *     // Create a url object;
 *
 *     $url = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor'
 *     $url = KService::get('koowa:http.url', array('url' => $url) );
 *
 *     // the $ur properties are ...
 *     //
 *     // $url->scheme   => 'http'
 *     // $url->host     => 'example.com'
 *     // $url->user     => 'anonymous'
 *     // $url->pass     => 'guest'
 *     // $url->path     => array('path', 'to', 'index.php', 'foo', 'bar')
 *     // $url->format   => 'xml'
 *     // $url->query    => array('baz' => 'dib')
 *     // $url->fragment => 'anchor'
 * ?>
 * </code>
 *
 * Now that we have imported the url and had it parsed automatically, we
 * can modify the component parts, then fetch a new url string.
 *
 * <code>
 * <?php
 *     // change to 'https://'
 *     $url->scheme = 'https';
 *
 *     // remove the username and password
 *     $url->user = '';
 *     $url->pass = '';
 *
 *     // change the value of 'baz' to 'zab'
 *     $url->setQuery('baz', 'zab');
 *
 *     // add a new query element called 'zim' with a value of 'gir'
 *     $url->query['zim'] = 'gir';
 *
 *     // reset the path to something else entirely.
 *     // this will additionally set the format to 'php'.
 *     $url->setPath('/something/else/entirely.php');
 *
 *     // add another path element
 *     $url->path[] = 'another';
 *
 *     // and fetch it to a string.
 *     $new_url = $url->getUrl();
 *
 *     // the $new_url string is as follows; notice how the format
 *     // is always applied to the last path-element.
 *     // /something/else/entirely/another.php?baz=zab&zim=gir#anchor
 *
 *     // Get the full URL to get the scheme and host
 *     $full_url = $url->getUrl(true);
 *
 *     // the $full_url string is:
 *     // https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
 * ?>
 * </code>
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Http
 */
class KHttpUrl extends KObject
{
    /**
     * The url parts
     *
     * @see get()
     */
    const SCHEME   = 1;
    const USER     = 2;
    const PASS     = 4;
    const HOST     = 8;
    const PORT     = 16;
    const PATH     = 32;
    const FORMAT   = 64;
    const QUERY    = 128;
    const FRAGMENT = 256;

    const AUTH     = 6;
    const BASE     = 127;
    const FULL     = 511;

    /**
     * The scheme [http|https|ftp|mailto|...]
     *
     * @var string
     */
    public $scheme = '';

    /**
     * The host specification (for example, 'example.com').
     *
     * @var string
     */
    public $host = '';

    /**
     * The port number (for example, '80').
     *
     * @var string
     */
    public $port = '';

    /**
     * The username, if any.
     *
     * @var string
     */
    public $user = '';

    /**
     * The password, if any.
     *
     * @var string
     */
    public $pass = '';

    /**
     * The path portion (for example, 'path/to/index.php').
     *
     * @var string
     */
    public $path = '';

    /**
     * The dot-format extension of the last path element (for example, the "rss"
     * in "feed.rss").
     *
     * @var string
     */
    public $format = '';

    /**
     * The query portion (for example baz=dib)
     *
     * Public access is allowed via __get() with $query.
     *
     * @var array
     *
     * @see setQuery()
     * @see getQuery()
     */
    protected $_query = array();

    /**
     * The fragment aka anchor portion (for example, the "foo" in "#foo").
     *
     * @var string
     */
    public $fragment = '';

    /**
     * Url-encode only these characters in path elements.
     *
     * Characters are ' ' (space), '/', '?', '&', and '#'.
     *
     * @var array
     */
    protected $_encode_path = array (
        ' ' => '+',
        '/' => '%2F',
        '?' => '%3F',
        '&' => '%26',
        '#' => '%23',
    );

    /**
     * Constructor
     *
     * @param   $config KConfig An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        parent::__construct($config);

        $this->setUrl($config->url);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   $config KConfig An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'url'  => '',
        ));

        parent::_initialize($config);
    }

    /**
     * Implements the virtual $query property.
     *
     * @param   $key string The virtual property to set.
     * @param   $val string Set the virtual property to this value.
     */
    public function __set($key, $val)
    {
        if ($key == 'query') {
            $this->setQuery($val);
        }

        if($key == 'path') {
            $this->setPath($val);
        }
    }

    /**
     * Implements access to $_query by reference so that it appears to be
     * a public $query property.
     *
     * @param   $key  string  The virtual property to return.
     * @return  array The value of the virtual property.
     */
    public function &__get($key)
    {
        if ($key == 'query') {
           return $this->_query;
        }
    }

    /**
     * Get the full url, of the format scheme://user:pass@host/path?query#fragment';
     *
     * @param   $parts  integer A bitmask of binary or'ed HTTP_URL constants; FULL is the default
     * @return  string
     */
    public function getUrl($parts = self::FULL)
    {
        $url = '';

        //Add the scheme
        if(($parts & self::SCHEME) && !empty($this->scheme)) {
            $url .=  urlencode($this->scheme).'://';
        }

        //Add the username and password
        if(($parts & self::USER) && !empty($this->user))
        {
            $url .= urlencode($this->user);
            if(($parts & self::PASS) && !empty($this->pass)) {
                $url .= ':' . urlencode($this->pass);
            }

            $url .= '@';
        }

        // Add the host and port, if any.
        if(($parts & self::HOST) && !empty($this->host))
        {
            $url .=  urlencode($this->host);

            if(($parts & self::PORT) && !empty($this->port)) {
                $url .=  ':' . (int) $this->port;
            }
        }

        // Add the rest of the url. we use trim() instead of empty() on string
        // elements to allow for string-zero values.
        if(($parts & self::PATH) && !empty($this->path))
        {
            $url .= $this->_pathEncode($this->path);
            if(($parts & self::FORMAT) && trim($this->format) !== '') {
                $url .= '.' . urlencode($this->format);
            }
        }

        $query = $this->getQuery();
        if(($parts & self::QUERY) && !empty($query)) {
            $url .= '?' . $this->getQuery();
        }

        if(($parts & self::FRAGMENT) && trim($this->fragment) !== '') {
            $url .=  '#' . urlencode($this->fragment);
        }

        return $url;
    }

    /**
     * Set the url
     *
     * @param   $url  string
     * @return  KHttpUrl
     */
    public function setUrl($url)
    {
        if(!empty($url))
        {
            $segments = parse_url($url);

            foreach ($segments as $key => $value) {
                $this->$key = $value;
            }

            if($this->format = pathinfo($this->path, PATHINFO_EXTENSION)) {
                $this->path = str_replace('.'.$this->format, '', $this->path);
            }
        }

        return $this;
    }

    /**
     * Sets the query string in the url, for KHttpUrl::getQuery() and KHttpUrl::$query.
     *
     * This will overwrite any previous values.
     *
     * @param   $query  string|array    The query string to use; for example `foo=bar&baz=dib`.
     * @return  KHttpUrl
     */
    public function setQuery($query)
    {
        if(!is_array($query))
        {
            if(strpos($query, '&amp;') !== false) {
               $query = str_replace('&amp;','&',$query);
            }

            //Set the query vars
            parse_str($query, $this->_query);
        }

        if(is_array($query)) {
            $this->_query = $query;
        }

        return $this;
    }

    /**
     * Returns the query portion as a string or array
     *
     * @param 	$toArray    boolean	    If TRUE return an array. Default FALSE
     * @return  string|array    The query string; e.g., `foo=bar&baz=dib`.
     */
    public function getQuery($toArray = false)
    {
		$result = $toArray ? $this->_query : http_build_query($this->_query, '', '&');
		return $result;
    }

    /**
     * Sets the KHttpUrl::$path array and $format from a string.
     *
     * This will overwrite any previous values. Also, resets the format based
     * on the final path value.
     *
     * @param   $path   string  The path string to use; for example,"/foo/bar/baz/dib". A leading slash will *not* create
     * an empty first element; if the string has a leading slash, it is ignored.
     * @return  KHttpUrl
     */
    public function setPath($path)
    {
        $spec = trim($path, '/');

        $this->path = array();
        if (! empty($path)) {
            $this->path = explode('/', $path);
        }

        foreach ($this->path as $key => $val) {
            $this->path[$key] = urldecode($val);
        }

        if ($val = end($this->path))
        {
            // find the last dot in the value
            $pos = strrpos($val, '.');

            if ($pos !== false)
            {
                $key = key($this->path);
                $this->format = substr($val, $pos + 1);
                $this->path[$key] = substr($val, 0, $pos);
            }
        }

        return $this;
    }


    /**
     * Return a string representation of this url.
     *
     * @see    getUrl()
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl(self::FULL);
    }

    /**
     * Converts an array of path elements into a string.
     *
     * Does not use urlencode(); instead, only converts characters found in KHttpUrl::$_encode_path.
     *
     * @param  $spec array The path elements.
     * @return string A url path string.
     */
    protected function _pathEncode($spec)
    {
        if (is_string($spec)) {
            $spec = explode('/', $spec);
        }

        $keys = array_keys($this->_encode_path);
        $vals = array_values($this->_encode_path);

        $out = array();
        foreach ((array) $spec as $elem) {
            $out[] = str_replace($keys, $vals, $elem);
        }

        return implode('/', $out);
    }
}
