<?php
/**
 * @version		$Id: database.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Database
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Database Namespace class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Database
 */
class KDatabase
{
	/**
	 * Database operations
	 */
	const OPERATION_SELECT = 'select';
	const OPERATION_INSERT = 'insert';
	const OPERATION_UPDATE = 'update';
	const OPERATION_DELETE = 'delete';
	const OPERATION_SHOW   = 'show';

	/**
	 * Database result mode
	 */
	const RESULT_STORE = 0;
	const RESULT_USE   = 1;

	/**
	 * Database fetch mode
	 */
	const FETCH_ROW         = 0;
	const FETCH_ROWSET      = 1;

	const FETCH_ARRAY       = 0;
	const FETCH_ARRAY_LIST  = 1;
	const FETCH_FIELD       = 2;
	const FETCH_FIELD_LIST  = 3;
	const FETCH_OBJECT      = 4;
	const FETCH_OBJECT_LIST = 5;

	/**
	 * Row states
	 */
	const STATUS_LOADED   = 'loaded';
	const STATUS_DELETED  = 'deleted';
    const STATUS_CREATED  = 'created';
    const STATUS_UPDATED  = 'updated';
    const STATUS_FAILED   = 'failed';
}