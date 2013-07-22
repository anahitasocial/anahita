<?php
/**
 * @version		$Id: example.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Example Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentExample extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentExample( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

	}

	/**
	 * Example after display title method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayTitle( &$article, &$params, $limitstart )
	{
		global $mainframe;

		return '';
	}

	/**
	 * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onBeforeDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

		return '';
	}

	/**
	 * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 * @return	string
	 */
	function onAfterDisplayContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

		return '';
	}

	/**
	 * Example before save content method
	 *
	 * Method is called right before content is saved into the database.
	 * Article object is passed by reference, so any changes will be saved!
	 * NOTE:  Returning false will abort the save with an error.
	 * 	You can set the error by calling $article->setError($message)
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	bool		If false, abort the save
	 */
	function onBeforeContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 *
	 * @param 	object		A JTableContent object
	 * @param 	bool		If the content is just about to be created
	 * @return	void
	 */
	function onAfterContentSave( &$article, $isNew )
	{
		global $mainframe;

		return true;
	}

}
