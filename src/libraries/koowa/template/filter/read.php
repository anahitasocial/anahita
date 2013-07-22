<?php
/**
* @version      $Id: read.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template Write Filter Interface
 *
 * Processes the template on input
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Filter
 */
interface KTemplateFilterRead
{
    /**
     * Parse the text and filter it
     *
     * @param string Block of text to parse
     * @return KTemplateFilterRead
     */
    public function read(&$text);
}