<?php
/**
 * @version 	$Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 */
interface KLoaderAdapterInterface
{
	/**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();

	/**
	 * Get the class prefix
	 *
	 * @return string	Returns the class prefix
	 */
	public function getPrefix();

	/**
	 * Get the base path
	 *
	 * @return string	Returns the base path
	 */
	public function getBasepath();

    /**
     * Get the path based on a class name
     *
     * @param  string           The class name
     * @return string|false     Returns the path on success FALSE on failure
     */
    public function findPath($classname, $basepath = null);
}