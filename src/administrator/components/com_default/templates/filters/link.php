<?php
/**
 * @version     $Id: link.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Script Filter
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterLink extends KTemplateFilterLink
{
    /**
     * Render script information
     *
     * @param string    The script information
     * @param array     Associative array of attributes
     * @return string
     */
    protected function _renderScript($link, $attribs = array())
    {
        if(KRequest::type() == 'AJAX') {
            return parent::_renderLink($script, $attribs);
        }

        $relType  = 'rel';
        $relValue = $attribs['rel'];
        unset($attribs['rel']);

        JFactory::getDocument()
            ->addHeadLink($link, $relValue, $relType, $attribs);
    }
}