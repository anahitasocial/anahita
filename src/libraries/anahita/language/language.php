<?php

/**
 * Language handling class
 *
 * @static
 * @package 	Anahita.Framework
 * @subpackage	Language
 * @since		4.3
 */
class AnLanguage extends KObject implements KServiceInstantiatable
{
    /**
    *   Debug mode
    */
    protected $_debug = false;

    /**
    *   Default language
    */
    protected $_default;

    /**
    *   Array of orphan text
    */
    protected $_orphans = array();

    /**
    *   Language meta data
    */
    protected $_meta = null;

    /**
    *   Selected language
    */
    protected $_language = null;

    /**
    *   List of language files that have been loade
    */
    protected $_paths = array();

    /**
    *   Array of translations
    */
    protected $_strings = array();

    /**
    *   Array of used text
    */
    protected $_used = array();

    /**
  	 * Constructor
  	 *
  	 * Prevent creating instances of this class by making the contructor private
  	 *
  	 * @param 	object 	An optional KConfig object with configuration options
  	 */
  	public function __construct(KConfig $config)
  	{
    	parent::__construct($config);

        $this->_default = $config->default;
        $this->_debug = $config->debug;

        $language = is_null($config->language) ? $this->_default : $config->language;
        $this->setLanguage($language);

        $this->load();
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
    		'debug' => false,
            'default' => 'en-GB'
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $instance = new AnLanguage($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
	 * Set the language attributes to the given language
	 * Once called, the language still needs to be loaded using AnLanguage::load()
	 *
	 * @access	public
	 * @param	string	$language	Language code
	 * @return	string	Previous value
	 */
	public function setLanguage($language)
	{
        if (! $this->exists($language)) {
            $language = $this->_default;
        }

        $this->_language = $language;
        $this->_meta = $this->getMetadata($this->_language);
        $local = $this->getLocale();
        setlocale(LC_TIME, $local);

		return $this;
	}

    /**
	* Translate function, mimics the php gettext (alias _) function
	*
	* @access	public
	* @param	string	$string The string to translate
	* @param	boolean	$jsSafe	Make the result javascript safe
	* @return	string	The translation of the string
	*/
	public function _($string, $jsSafe = false)
	{
		//$key = str_replace( ' ', '_', strtoupper( trim( $string ) ) );echo '<br />'.$key;
        $key = strtoupper($string);
		$key = (substr($key, 0, 1) === '_') ? substr($key, 1) : $key;

		if (isset($this->_strings[$key])) {

            $string = $this->_debug ? "&bull;".$this->_strings[$key]."&bull;" : $this->_strings[$key];

            if ($this->_debug) {
                $caller = $this->_getCallerInfo();

                if (!array_key_exists($key, $this->_used)) {
					$this->_used[$key] = array();
				}

				$this->_used[$key][] = $caller;
			}

		} else {

            if (defined($string)) {

                $string = $this->_debug ? '!!'.constant($string).'!!' : constant($string);

				// Store debug information
				if ( $this->_debug ) {
                    $caller = $this->_getCallerInfo();

					if (!array_key_exists($key, $this->_used)) {
						$this->_used[$key] = array();
					}

					$this->_used[$key][] = $caller;
				}

			} elseif ($this->_debug) {

                $caller	= $this->_getCallerInfo();
				$caller['string'] = $string;

				if(!array_key_exists($key, $this->_orphans)) {
					$this->_orphans[$key] = array();
				}

				$this->_orphans[$key][] = $caller;
				$string = '??'.$string.'??';
			}
		}

		if ($jsSafe) {
			$string = addslashes($string);
		}

		return $string;
	}

    /**
	 * Returns a associative array holding the metadata
	 *
	 * @access	public
	 * @param	string	The name of the language
	 * @return	mixed	If $language exists return key/value pair with the language metadata,
	 *  				otherwise return NULL
	 */
	public function getMetadata($language)
	{
		$path = $this->getLanguagePath(ANPATH_BASE, $language);
		$file = $language.'.json';

		$meta = null;

		if (file_exists($path.DS.$file)) {
			$result = json_decode(file_get_contents($path.DS.$file));
			$meta = $result->meta;
			$meta->tag = $result->tag;
		}

		return $meta;
	}

    /**
	 * Get a matadata language property
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default	The default value
	 * @return	mixed The value of the property
	 */
	public function get($property = null, $default = null)
	{
		if (isset($this->_meta->property)) {
			return $this->_meta->property;
		}

		return $default;
	}

    /**
	 * Get the path to a language
	 *
	 * @access	public
	 * @param	string $basePath  The basepath to use
	 * @param	string $language The language tag
	 * @return	string	language related path or null
	 */
	public function getLanguagePath($basePath = ANPATH_BASE, $language = null )
	{
		$dir = $basePath.DS.'language';

        if (!empty($language)) {
			$dir .= DS.$language;
		}

		return $dir;
	}

    /**
	 * Get a list of language files that have been loaded
	 *
	 * @access	public
	 * @param	string	$extension	An option extension name
	 * @return	array
	 */
	public function getPaths($extension = null)
	{
		if (isset($extension)) {

            if (isset($this->_paths[$extension])) {
				return $this->_paths[$extension];
            }

			return array();
		}
		else
		{
			return $this->_paths;
		}
	}

    /**
	* Get locale property
	*
	* @access	public
	* @return	array of strings
	*/
	public function getLocale()
	{
        $locales = explode(',', $this->_meta->locale);

		for($i = 0; $i < count($locales); $i++ ) {
			$locale = $locales[$i];
			$locale = trim($locale);
			$locales[$i] = $locale;
		}

		return $locales;
	}

    /**
	* Get the RTL property
	*
	* @access	public
	* @return	boolean True is it an RTL language
	*/
	public function isRTL() {
		return $this->_meta->rtl;
	}

    /**
	* Get for the language tag (as defined in RFC 3066)
	*
	* @access	public
	* @return	string The language tag
	*/
	public function getTag() {
		return $this->_meta->tag;
	}

    /**
	* Set the Debug property
	*
	* @access	public
	* @return	AnLanguage Object
	*/
	public function setDebug($debug) {
		$this->_debug = $debug;
		return $this;
	}

	/**
	* Get the Debug property
	*
	* @access	public
	* @return	boolean True is in debug mode
	*/
	public function getDebug() {
		return $this->_debug;
	}

    /**
	 * Get the default language code
	 *
	 * @access	public
	 * @return	string Language code
	 */
	public function getDefault() {
		return $this->_default;
	}

	/**
	 * Set the default language code
	 *
	 * @access	public
	 * @return	string Previous value
	 * @since	1.5
	 */
	public function setDefault($language) {
		$this->_default	= $language;
		return $this;
	}

    /**
	 * Get the list of used strings
	 * Used strings are those strings requested and found either as a string or a constant
	 *
	 * @access	public
	 * @return	array Used strings
	 */
	function getUsed() {
		return $this->_used;
	}

	/**
	 * Determines is a key exists
	 *
	 * @access	public
	 * @param	key $key The key to check
	 * @return	boolean True, if the key exists
	 */
	function hasKey($key) {
		return isset ($this->_strings[strtoupper($key)]);
	}

    /**
	 * Loads a single language file and appends the results to the existing strings
	 *
	 * @access	public
	 * @param	string 	$extension 	The extension for which a language file should be loaded
	 * @param	string 	$basePath  	The basepath to use
	 * @param	string	$lang		The language to load, default null for the current language
	 * @param	boolean $reload		Flag that will force a language to be reloaded if set to true
	 * @return	boolean	True, if the file has successfully loaded.
	 */
	public function load($extension = 'anahita', $basePath = ANPATH_BASE, $language = null, $reload = false)
	{
		if (is_null($language)) {
			$language = $this->_language;
		}

        $filename = ($extension === 'anahita') ?  $language : $language.'.'. $extension;

		if (isset($this->_paths[$extension][$filename]) && !$reload) {
            return true;
		} else {
            $path = $this->getLanguagePath($basePath, $this->_language);
            $filename = $path.DS.$filename.'.ini';
            $result = $this->_load($filename, $extension, false);

            return $result;
		}
	}

    /**
	* Loads a language file
	*
	* @access	protected
	* @param	string The name of the file
	* @param	string The name of the extension
	* @return	boolean True if new strings have been added to the language
	* @see		AnLanguage::load()
	*/
	protected function _load($filename, $extension = 'anahita', $overwrite = true)
	{
		$result	= false;

        if (file_exists($filename)) {

            $strings = parse_ini_file($filename, false, INI_SCANNER_RAW);

            if (is_array($strings)){

                if ($overwrite) {
                    $this->_strings = array_merge($this->_strings, $strings);
                } else {
                    $this->_strings = array_merge($strings, $this->_strings);
                }

                $result = true;
			}
        }

		// Record the result of loading the extension's file.
		if (!isset($this->_paths[$extension])) {
			$this->_paths[$extension] = array();
		}

		$this->_paths[$extension][$filename] = $result;

		return $result;
	}

    /**
	 * Determine who called AnLanguage or AnTranslate
	 *
	 * @access	private
	 * @return	array Caller information
	 */
	protected function _getCallerInfo()
	{
		if (!function_exists('debug_backtrace')) {
			return null;
		}

		$backtrace = debug_backtrace();
		$info = array();
		$continue = true;

		while ($continue && next($backtrace)) {

			$step = current($backtrace);
			$class = @$step['class'];

			// We're looking for something outside of language.php
			if ($class != 'AnLanguage' && $class != 'AnTranslate') {

                $info['function']	= @$step['function'];
				$info['class']		= $class;
				$info['step']		= prev($backtrace);

				// Determine the file and name of the file
				$info['file'] = @ $step['file'];
				$info['line'] = @ $step['line'];

				$continue = false;
			}
		}

		return $info;
	}

    /**
	 * Check if a language exists
	 *
	 * @param	string $lang Language to check
	 * @param	string $basePath Optional path to check
	 * @return	boolean True if the language exists
	 */
	public function exists($language, $basePath = ANPATH_BASE)
	{
        $directory = $this->getLanguagePath($basePath, $language);
        return is_dir($directory);
	}
}
