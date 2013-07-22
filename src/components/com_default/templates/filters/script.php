<?php
/**
 * @version     $Id: default.php 2721 2010-10-27 00:58:51Z johanjanssens $
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
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterScript extends KTemplateFilterScript
{
    /**
     * Render script information
     *
     * @param string    The script information
     * @param boolean   True, if the script information is a URL.
     * @param array     Associative array of attributes
     * @return string
     */
    protected function _renderScript($script, $link, $attribs = array())
    {
        if(KRequest::type() == 'AJAX') {
            return parent::_renderScript($script, $link, $attribs);
        }

        $document = JFactory::getDocument();

        if($link) {
            $document->addScript($script, 'text/javascript');
        } else {
            $document->addScriptDeclaration($script);
        }
    }
}