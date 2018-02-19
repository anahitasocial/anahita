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
class LibBaseTemplateFilterShorttag extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterRead
{
    /**
     * Convert <?= ?> to long-form <?php echo ?> when needed
     *
     * @param string
     * @return LibBaseTemplateFilterShorttag
     */
    public function read(&$text)
    {
        if (!ini_get('short_open_tag')) {
            /**
         	* We could also convert <%= like the real T_OPEN_TAG_WITH_ECHO
         	* but that's not necessary.
         	*
         	* It might be nice to also convert PHP code blocks <? ?> but
         	* let's quit while we're ahead.  It's probably better to keep
         	* the <?php for larger code blocks but that's your choice.  If
          	* you do go for it, explicitly check for <?xml as this will
         	* probably be the biggest headache.
         	*/

            // convert "<?=" to "<?php echo"
               $find = '/\<\?\s*=\s*(.*?)/';
            $replace = "<?php echo \$1";
            $text = preg_replace($find, $replace, $text);

            // convert "<?" to "<?php"
            $find = '/\<\?(?:php)?\s*(.*?)/';
            $replace = "<?php \$1";
            $text = preg_replace($find, $replace, $text);
        }

        return $this;
    }
}
