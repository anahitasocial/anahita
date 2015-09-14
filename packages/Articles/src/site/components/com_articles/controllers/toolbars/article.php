<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Articles
 * @subpackage Controller_Toolbar
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Article Toolbar 
 *
 * @category   Anahita
 * @package    Com_Articles
 * @subpackage Controller_Toolbar
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComArticlesControllerToolbarArticle extends ComMediumControllerToolbarDefault
{	    
	/**
	 * Add Admin Commands for an entity
     * 
	 * @return void
	 */
	public function addAdministrationCommands()
	{
	    $this->addCommand('pin');
	
	    parent::addAdministrationCommands();
	}
}