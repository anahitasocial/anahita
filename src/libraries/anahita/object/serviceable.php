<?php
/**
 * @package     Anahita_Object
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
interface AnObjectServiceable
{
	/**
	 * Get an instance of a class based on a class identifier only creating it
	 * if it doesn't exist yet.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
	 * @param	array  			$config     An optional associative array of configuration settings.
	 * @throws	AnObjectException
	 * @return	object  		Return object on success, throws exception on failure
	 */
	public function getService($identifier, array $config = array());

	/**
	 * Get a service identifier.
	 *
     * @param	string|object	$identifier The class identifier or identifier object
	 * @return	AnServiceIdentifier
	 */
	public function getIdentifier($identifier = null);
}