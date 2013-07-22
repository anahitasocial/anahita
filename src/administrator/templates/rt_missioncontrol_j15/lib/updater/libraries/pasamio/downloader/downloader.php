<?php
/**
 * Downloader Function
 */

RokUpdater::import('joomla.base.adapter');

class Downloader extends JAdapter {

	/**
	 * PHP style constructor!
	 */
	function __construct()
	{
		// base directory is here (so here/adapters)
		// and the prefix is 'downloader'
		parent::__construct(dirname(__FILE__), 'Downloader');
	}

	/**
	 * Returns a reference to the global Downloader object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return      object  An downloader object
	 * @since 1.5
	 */
	public static function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new Downloader();
		}
		return $instance;
	}

	/**
	 * Gets a file name out of a url
	 *
	 * @static
	 * @param string $url URL to get name from
	 * @return mixed String filename or boolean false if failed
	 * @since 1.5
	 */
	function getFilenameFromURL($url)
	{
		if (is_string($url)) {
			$parts = explode('/', $url);
			return $parts[count($parts) - 1];
		}
		return false;
	}

}