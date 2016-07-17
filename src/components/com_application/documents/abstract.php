<?php

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

abstract class ComApplicationDocumentAbstract extends KObject implements ComApplicationDocumentInterface
{
    /**
	 * Document title
	 *
	 * @var	 string
	 * @access  public
	 */
	public $title = '';

	/**
	 * Document description
	 *
	 * @var	 string
	 * @access  public
	 */
	public $description = '';

	/**
	 * Document full URL
	 *
	 * @var	 string
	 * @access  public
	 */
	public $link = '';

	/**
	 * Document base URL
	 *
	 * @var	 string
	 * @access  public
	 */
	public $base = '';

	 /**
	 * Contains the document language setting
	 *
	 * @var	 string
	 * @access  public
	 */
	public $language = 'en-GB';

	/**
	 * Contains the document direction setting
	 *
	 * @var	 string
	 * @access  public
	 */
	public $direction = 'ltr';

	/**
	 * Document modified date
	 *
	 * @var		string
	 * @access   protected
	 */
	protected $_mdate = '';

	/**
	 * Tab string
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_tab = "\11";

	/**
	 * Contains the line end string
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_lineEnd = "\12";

	/**
	 * Contains the character encoding string
	 *
	 * @var	 string
	 * @access  protected
	 */
	protected $_charset = 'utf-8';

	/**
	 * Document mime type
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_mime = '';

	/**
	 * Document namespace
	 *
	 * @var		string
	 * @access   protected
	 */
	protected $_namespace = '';

	/**
	 * Document profile
	 *
	 * @var		string
	 * @access   protected
	 */
	protected $_profile = '';

	/**
	 * Array of linked scripts
	 *
	 * @var		array
	 * @access   protected
	 */
	protected $_scripts = array();

	/**
	 * Array of scripts placed in the header
	 *
	 * @var  array
	 * @access   protected
	 */
	protected $_script = array();

	 /**
	 * Array of linked style sheets
	 *
	 * @var	 array
	 * @access  protected
	 */
	protected $_styleSheets = array();

	/**
	 * Array of included style declarations
	 *
	 * @var	 array
	 * @access  protected
	 */
	protected $_style = array();

	/**
	 * Array of meta tags
	 *
	 * @var	 array
	 * @access  protected
	 */
	protected $_metaTags = array();

	/**
	 * The document type
	 *
	 * @var	 string
	 * @access  protected
	 */
	protected $_type = null;

	/**
	 * Array of buffered output
	 *
	 * @var		mixed (depends on the renderer)
	 * @access	protected
	 */
	protected $_buffer = null;

    /**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct(KConfig $config = null)
	{
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();

        // Initialize the options
        parent::__construct($config);

		$this->setLineEnd($config->lineend);
        $this->setCharset($config->charset);
        $this->setLanguage($config->language);
        $this->setDirection($config->direction);
        $this->setTab($config->tab);
        $this->setLink($config->link);
        $this->setBase($config->base);
        $this->setType($config->type);
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
    	$config->append(array(
    		'lineend' => "\12",
    		'charset' => 'utf-8',
       	 	'language' => 'en-GB',
    	    'direction'	=> 'ltr',
    		'tab' => "\11",
    		'link'   => '',
    		'base'  => '',
            'type' => 'html'
        ));

        parent::_initialize($config);
    }

    /**
	 * Returns a reference to the global JDocument object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $document = &JDocument::getInstance();</pre>
	 *
	 * @access public
	 * @param type $type The document type to instantiate
	 * @return object  The document object.
	 */
	public function getInstance($type = 'html', $attributes = array())
	{
        $registry = $this->getService('application.registry');
        $offset = 'application-document';

        if (!$registry->offsetExists($offset)) {
            $document = $this->getService('com:application.document.'.$type);
            $registry->offsetSet($offset, $document);
        }

        return $registry->offsetGet($offset);
    }

    public function getStylesheets()
    {
        return $this->_styleSheets;
    }

    public function getStyle()
    {
        return $this->_style;
    }

    public function getScripts()
    {
        return $this->_scripts;
    }

    public function getScript()
    {
        return $this->_script;
    }

    /**
     * Set the document type
     *
     * @access	public
     * @param	string $type
     */
    public function setType($type) {
    	$this->_type = $type;
    }

	 /**
	 * Returns the document type
	 *
	 * @access	public
	 * @return	string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Get the document head data
	 *
	 * @access	public
	 * @return	array	The document head data in array form
	 */
	public function getHeadData() {
		// Impelemented in child classes
	}

	/**
	 * Set the document head data
	 *
	 * @access	public
	 * @param	array	$data	The document head data in array form
	 */
	public function setHeadData($data) {
		// Impelemented in child classes
	}

	/**
	 * Get the contents of the document buffer
	 *
	 * @access public
	 * @return 	The contents of the document buffer
	 */
	public function getBuffer() {
		return $this->_buffer;
	}

	/**
	 * Set the contents of the document buffer
	 *
	 * @access public
	 * @param string 	$content	The content to be set in the buffer
	 */
	public function setBuffer($contents, $type, $name = null) {
		$this->_buffer = $content;
	}

	/**
	 * Gets a meta tag.
	 *
	 * @param	string	$name			Value of name or http-equiv tag
	 * @param	bool	$http_equiv	 META type "http-equiv" defaults to null
	 * @return	string
	 * @access	public
	 */
	public function getMetaData($name, $http_equiv = false)
	{
		$result = '';
		$name = strtolower($name);

        if($name == 'description') {
			$result = $this->getDescription();
		} else {

        	if ($http_equiv == true) {
				$result = @$this->_metaTags['http-equiv'][$name];
			} else {
				$result = @$this->_metaTags['standard'][$name];
			}
		}

        return $result;
	}

	/**
	 * Sets or alters a meta tag.
	 *
	 * @param string  $name			Value of name or http-equiv tag
	 * @param string  $content		Value of the content tag
	 * @param bool	$http_equiv	 META type "http-equiv" defaults to null
	 * @return void
	 * @access public
	 */
	public function setMetaData($name, $content, $http_equiv = false)
	{
		$name = strtolower($name);

        if($name == 'description') {
			$this->setDescription($content);
		} else {

        	if ($http_equiv == true) {
				$this->_metaTags['http-equiv'][$name] = $content;
			} else {
				$this->_metaTags['standard'][$name] = $content;
			}
		}
	}

	 /**
	 * Adds a linked script to the page
	 *
	 * @param	string  $url		URL to the linked script
	 * @param	string  $type		Type of script. Defaults to 'text/javascript'
	 * @access   public
	 */
	public function addScript($url, $type="text/javascript") {
		$this->_scripts[$url] = $type;
	}

	/**
	 * Adds a script to the page
	 *
	 * @access   public
	 * @param	string  $content   Script
	 * @param	string  $type	Scripting mime (defaults to 'text/javascript')
	 * @return   void
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		if (!isset($this->_script[strtolower($type)])) {
			$this->_script[strtolower($type)] = $content;
		} else {
			$this->_script[strtolower($type)] .= chr(13).$content;
		}
	}

	/**
	 * Adds a linked stylesheet to the page
	 *
	 * @param	string  $url	URL to the linked style sheet
	 * @param	string  $type   Mime encoding type
	 * @param	string  $media  Media type that this stylesheet applies to
	 * @access   public
	 */
	public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array())
	{
		$this->_styleSheets[$url]['mime']		= $type;
		$this->_styleSheets[$url]['media']		= $media;
		$this->_styleSheets[$url]['attribs']	= $attribs;
	}

	 /**
	 * Adds a stylesheet declaration to the page
	 *
	 * @param	string  $content   Style declarations
	 * @param	string  $type		Type of stylesheet (defaults to 'text/css')
	 * @access   public
	 * @return   void
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		if (!isset($this->_style[strtolower($type)])) {
			$this->_style[strtolower($type)] = $content;
		} else {
			$this->_style[strtolower($type)] .= chr(13).$content;
		}
	}

	 /**
	 * Sets the document charset
	 *
	 * @param   string   $type  Charset encoding string
	 * @access  public
	 * @return  void
	 */
	public function setCharset($type = 'utf-8') {
		$this->_charset = $type;
	}

	/**
	 * Returns the document charset encoding.
	 *
	 * @access public
	 * @return string
	 */
	public function getCharset() {
		return $this->_charset;
	}

	/**
	 * Sets the global document language declaration. Default is English (en-gb).
	 *
	 * @access public
	 * @param   string   $lang
	 */
	public function setLanguage($lang = "en-GB") {
		$this->language = strtolower($lang);
	}

	/**
	 * Returns the document language.
	 *
	 * @return string
	 * @access public
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Sets the global document direction declaration. Default is left-to-right (ltr).
	 *
	 * @access public
	 * @param   string   $lang
	 */
	public function setDirection($dir = "ltr") {
		$this->direction = strtolower($dir);
	}

	/**
	 * Returns the document language.
	 *
	 * @return string
	 * @access public
	 */
	public function getDirection() {
		return $this->direction;
	}

	/**
	 * Sets the title of the document
	 *
	 * @param	string	$title
	 * @access   public
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Return the title of the document.
	 *
	 * @return   string
	 * @access   public
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the base URI of the document
	 *
	 * @param	string	$base
	 * @access   public
	 */
	public function setBase($base) {
		$this->base = $base;
	}

	/**
	 * Return the base URI of the document.
	 *
	 * @return   string
	 * @access   public
	 */
	public function getBase() {
		return $this->base;
	}

	/**
	 * Sets the description of the document
	 *
	 * @param	string	$title
	 * @access   public
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Return the title of the page.
	 *
	 * @return   string
	 * @access   public
	 */
	public function getDescription() {
		return $this->description;
	}

	 /**
	 * Sets the document link
	 *
	 * @param   string   $url  A url
	 * @access  public
	 * @return  void
	 */
	public function setLink($url) {
		$this->link = $url;
	}

	/**
	 * Returns the document base url
	 *
	 * @access public
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	 /**
	 * Sets the document generator
	 *
	 * @param   string
	 * @access  public
	 * @return  void
	 */
	public function setGenerator($generator) {
		$this->_generator = $generator;
	}

	/**
	 * Returns the document generator
	 *
	 * @access public
	 * @return string
	 */
	public function getGenerator() {
		return $this->_generator;
	}

	 /**
	 * Sets the document modified date
	 *
	 * @param   string
	 * @access  public
	 * @return  void
	 */
	public function setModifiedDate($date) {
		$this->_mdate = $date;
	}

	/**
	 * Returns the document modified date
	 *
	 * @access public
	 * @return string
	 */
	public function getModifiedDate() {
		return $this->_mdate;
	}

	 /**
	 * Sets the document MIME encoding that is sent to the browser.
	 *
	 * <p>This usually will be text/html because most browsers cannot yet
	 * accept the proper mime settings for XHTML: application/xhtml+xml
	 * and to a lesser extent application/xml and text/xml. See the W3C note
	 * ({@link http://www.w3.org/TR/xhtml-media-types/
	 * http://www.w3.org/TR/xhtml-media-types/}) for more details.</p>
	 *
	 * @param	string	$type
	 * @access   public
	 * @return   void
	 */
	public function setMimeEncoding($type = 'text/html') {
		$this->_mime = strtolower($type);
	}

	 /**
	 * Sets the line end style to Windows, Mac, Unix or a custom string.
	 *
	 * @param   string  $style  "win", "mac", "unix" or custom string.
	 * @access  public
	 * @return  void
	 */
	public function setLineEnd($style)
	{
		switch ($style) {
			case 'win':
				$this->_lineEnd = "\15\12";
				break;
			case 'unix':
				$this->_lineEnd = "\12";
				break;
			case 'mac':
				$this->_lineEnd = "\15";
				break;
			default:
				$this->_lineEnd = $style;
		}
	}

	/**
	 * Returns the lineEnd
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _getLineEnd() {
		return $this->_lineEnd;
	}

	/**
	 * Sets the string used to indent HTML
	 *
	 * @param	 string	$string	 String used to indent ("\11", "\t", '  ', etc.).
	 * @access	public
	 * @return	void
	 */
	public function setTab($string) {
		$this->_tab = $string;
	}

	 /**
	 * Returns a string containing the unit for indenting HTML
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function _getTab() {
        return $this->_tab;
	}

	/**
	 * Outputs the document
	 *
	 * @access public
	 * @param boolean 	$cache		If true, cache the output
	 * @param boolean 	$compress	If true, compress the output
	 * @param array		$params		Associative array of attributes
	 * @return 	The rendered data
	 */
	function render( $cache = false, $params = array())
	{
		JResponse::setHeader( 'Expires', gmdate( 'D, d M Y H:i:s', time() + 900 ) . ' GMT' );

        if ($mdate = $this->getModifiedDate()) {
			JResponse::setHeader( 'Last-Modified', $mdate /* gmdate( 'D, d M Y H:i:s', time() + 900 ) . ' GMT' */ );
		}

		JResponse::setHeader( 'Content-Type', $this->_mime .  '; charset=' . $this->_charset);
	}
}
