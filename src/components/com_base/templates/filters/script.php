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
 * Script Filter.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateFilterScript extends LibBaseTemplateFilterScript
{
    /**
     * Render script information.
     *
     * @param string    The script information
     * @param bool   True, if the script information is a URL.
     * @param array     Associative array of attributes
     *
     * @return string
     */
    protected function _renderScript($script, $link, $attribs = array())
    {
        if ($link) {
            $script = pick($this->getService('com:base.template.asset')->getURL($script), $script);
        }

        //if ajax try to get the content of the file
        if (KRequest::type() == 'AJAX') {
            if ($link) {
                $file = $this->getService('com:base.template.asset')->getFilePath($script);
                if ($file) {
                    $script = file_get_contents($file);
                    $link = false;
                }
            }

            return parent::_renderScript($script, $link, $attribs);
        }

        $document = KService::get('anahita:document');

        if($link) {
            return $document->addScript($script, 'text/javascript');
        } else {
            return $document->addScriptDeclaration($script);
        }
    }
}
