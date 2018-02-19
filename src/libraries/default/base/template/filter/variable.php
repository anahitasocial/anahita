<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateFilterVariable extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterRead
{
    /**
     * Convert '@' to '$this->', unless when they are escaped '\@'
     *
     * @param string
     * @return LibBaseTemplateFilterVariable
     */
    public function read(&$text)
    {
        /**
         * We could make a better effort at only finding @ between <?php ?>
         * but that's probably not necessary as @ doesn't occur much in the wild
         * and there's a significant performance gain by using str_replace().
         */

        // Replace \@ with \$
        $text = str_replace('\@', '\$', $text);

        // Now replace non-eescaped @'s
         $text = str_replace(array('@$'), '$', $text);

        // Replace \$ with @
        $text = str_replace('\$', '@', $text);

        return $this;
    }
}
