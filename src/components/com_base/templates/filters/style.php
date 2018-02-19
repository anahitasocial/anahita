<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Style Filter.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateFilterStyle extends LibBaseTemplateFilterStyle
{
    /**
     * Render style information.
     *
     * @param string    The style information
     * @param bool   True, if the style information is a URL
     * @param array     Associative array of attributes
     *
     * @return string
     */
    protected function _renderStyle($style, $link, $attribs = array())
    {
        //get the correct URL
        if ($link) {
            $style = pick($this->getService('com:base.template.asset')->getURL($style), $style);
        }

        //if ajax try to get the content of the file
        if (KRequest::type() == 'AJAX') {
            if ($link) {
                $file = $this->getService('com:base.template.asset')->getFilePath($style);
                if ($file) {
                    $style = file_get_contents($file);
                    $link = false;
                }
            }

            return parent::_renderStyle($style, $link, $attribs);
        }

        $document = KService::get('anahita:document');

        if($link) {
            return $document->addStyleSheet($style, 'text/css', null, $attribs);
        } else {
            return $document->addStyleDeclaration($style);
        }
    }
}
