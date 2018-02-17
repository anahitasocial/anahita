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
class AnLog extends KObject implements KServiceInstantiatable
{
    /**
	 * Log File Pointer
	 * @var	resource
	 */
	protected $_file = null;

	/**
	 * Log File Path
	 * @var	string
	 */
	protected $_path = '';

	/**
	 * Log Format
	 * @var	string
	 */
	protected $_format = '';

    /**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * Recognized key values include 'command_chain', 'charset', 'table_prefix',
	 * (this list is not meant to be comprehensive).
	 */
	public function __construct(KConfig $config = null)
	{
        parent::__construct($config);

        $this->_file = $config->file;
        $this->_format = $config->format;
        $this->_path = $config->path;
    }

    /**
    * class destructor closes the file
    *
    * @return void
    */
    public function __destruct()
	{
        $this->_closeLog();
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
         $settings = $this->getService('com:settings.setting');

         $config->append(array(
            'file' => 'error.php',
            'format' => "{DATE}\t{TIME}\t{C-IP}\t{STATUS}\t{COMMENT}"
        ))->append(array(
            'path' => $settings->log_path.DS.$config->file
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
            $instance = new AnLog($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
    * Adds a log entry
    *
    * @param Array(KEY1=>value1, KEY2=value2, ...) keys must be all upper case
    *
    * @return boolean TRUE if success
    */
    public function addEntry($entry)
	{
		// Set some default field values if not already set.
		$date = new AnDate();

		if (!isset ($entry['date'])) {
			$entry['date'] = $date->format("%Y-%m-%d");
		}

		if (!isset ($entry['time'])) {
			$entry['time'] = $date->format("%H:%M:%S");
		}

		if (!isset ($entry['c-ip'])) {
			$entry['c-ip'] = $_SERVER['REMOTE_ADDR'];
		}

		// Ensure that the log entry keys are all uppercase
		$entry = array_change_key_case($entry, CASE_UPPER);

		// Find all fields in the format string
		$fields = array ();
		$regex = "/{(.*?)}/i";
		preg_match_all($regex, $this->_format, $fields);

		// Fill in the field data
		$line = $this->_format;

		for ($i = 0; $i < count($fields[0]); $i++) {
			$line = str_replace($fields[0][$i], (isset ($entry[$fields[1][$i]])) ? $entry[$fields[1][$i]] : "-", $line);
		}

		// Write the log entry line
		if ($this->_openLog()) {
            return fputs($this->_file, "\n" . $line) ? true : false;
		}

        return false;
	}

    /**
	 * Open the log file pointer and create the file if it doesn't exist
	 *
	 * @access 	public
	 * @return 	boolean	True on success
	 */
	protected function _openLog()
	{
		// Only open if not already opened...
		if (is_resource($this->_file)) {
			return true;
		}

		$now = new AnDate();
		$date = $now->getDate();

		if (!file_exists($this->_path)) {

            $header[] = "#<? die('Direct Access To Log Files Not Permitted'); ?>";
			$header[] = "#Version: 1.0";
			$header[] = "#Date: " . $date;

			// Prepare the fields string
			$fields = str_replace("{", "", $this->_format);
			$fields = str_replace("}", "", $fields);
			$fields = strtolower($fields);
			$header[] = "#Fields: " . $fields;

			$head = implode("\n", $header);

		} else {
			$head = false;
		}

		if (!$this->_file = fopen($this->_path, "a")) {
			return false;
		}

		if ($head && !fputs($this->_file, $head)) {
			return false;
		}

		return true;
	}

    /**
	 * Close the log file pointer
	 *
	 * @access 	public
	 * @return 	boolean	True on success
	 */
	protected function _closeLog()
	{
		if (is_resource($this->_file)) {
			fclose($this->_file);
		}

		return true;
	}
}
