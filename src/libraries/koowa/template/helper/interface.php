<?php
/**
* @version      $Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template helper interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage	Helper
 */
interface KTemplateHelperInterface
{
 	/**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate();
}