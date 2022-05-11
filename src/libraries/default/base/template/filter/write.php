<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
interface LibBaseTemplateFilterWrite
{
    /**
     * Parse the text and filter it
     *
     * @param string Block of text to parse
     * @return LibBaseTemplateFilterWrite
     */
    public function write(&$text);
}
