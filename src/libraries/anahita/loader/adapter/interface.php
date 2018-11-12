<?php
/**
 * @package     Anahita_Loader
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
interface AnLoaderAdapterInterface
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