<?php
/**
 * @version		$Id: admin.admin.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Admin
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JApplicationHelper::getPath( 'admin_html' ) );

switch ($task)
{
	case 'sysinfo':
		HTML_admin_misc::system_info( );
		break;

	case 'changelog':
		HTML_admin_misc::changelog();
		break;

	case 'help':
		HTML_admin_misc::help();
		break;

	case 'version':
		HTML_admin_misc::version();
		break;

	case 'preview':
		HTML_admin_misc::preview();
		break;

	case 'preview2':
		HTML_admin_misc::preview( 1 );
		break;

	case 'keepalive':
		return;
		break;
}