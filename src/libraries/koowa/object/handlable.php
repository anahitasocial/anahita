<?php
/**
 * @version		$Id: handlable.php 4635 2012-05-13 16:08:49Z johanjanssens $
 * @package		Koowa_Object
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Object Hashable interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Object
 */
interface KObjectHandlable
{
	/**
	 * Get the object handle
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique, or NULL
	 */
	public function getHandle();
}