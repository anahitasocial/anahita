<?php

/**
 * Contentfilter Interface.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
interface PlgContentfilterInterface
{
    /**
      * Filter a text.
      *
      * @param string $value Value to be filtered
      *
      * @return string the filtered value
      */
     public function filter($data);
}
