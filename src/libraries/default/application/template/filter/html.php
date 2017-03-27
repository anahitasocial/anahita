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
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibApplicationTemplateFilterHtml extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Convert the alias.
     *
     * @param string
     *
     * @return KTemplateFilterAlias
     */
    public function write(&$text)
    {
        $matches = array();

        if (strpos($text, '<html')) {

            //add language
            $text = str_replace('<html', '<html lang="'.$this->getService('anahita:language')->getTag().'"', $text);

            //render the styles
            $text = str_replace('</head>', $this->_renderHead().$this->_renderStyles().'</head>', $text);

            //render the scripts
            $text = str_replace('</body>', $this->_renderScripts().'</body>', $text);
        }
    }

    /**
     * Render title.
     *
     * @return string
     */
    protected function _renderHead()
    {
        $document = $this->getService('anahita:document');
        $html = '<base href="base://" />';

        $title = str_replace(array('#', '@'), '', $document->getTitle());
        $html .= '<title>'.$document->getTitle().'</title>';

        $description = $document->getDescription();
        $stripURLRegex = "/((?<!=\")(http|ftp)+(s)?:\/\/[^<>()\s]+)/i";
        $description = preg_replace($stripURLRegex, '', $description);
        $description = strip_tags($description);
        $description = str_replace(array('#', '@'), '', $description);
        $description = KService::get('com:base.template.helper.text')->truncate($description, array('length' => 160));
        $description = trim($description);

        $html .= '<meta name="description" content="'.$description.'" />';

        return $html;
    }

    /**
     * Return the document scripts.
     *
     * @return string
     */
    protected function _renderScripts()
    {
        $document = $this->getService('anahita:document');

        $string = '';
        $string .= $this->_template->getHelper('javascript')->language('lib_anahita');

        $scripts = array_reverse($document->getScripts());

        foreach ($scripts as $src => $type) {
            $string .= '<script type="'.$type.'" src="'.$src.'"></script>';
        }

        $script = $document->getScript();

        foreach ($script as $type => $content) {
            $string .= '<script type="'.$type.'">'.$content.'</script>';
        }

        return $string;
    }

    /**
     * Return the document styles.
     *
     * @return string
     */
    protected function _renderStyles()
    {
        $document = $this->getService('anahita:document');
        $html = '';

        // Generate stylesheet links
        foreach ($document->getStyleSheets() as $src => $attr) {

            $rel = 'stylesheet';

            if (strpos($src, '.less')) {
                $rel .= '/less';
            }

            $html .= '<link rel="'.$rel.'" href="'.$src.'" type="'.$attr['mime'].'"';

            if (isset($attr['media'])) {
                $html .= ' media="'.$attr['media'].'" ';
            }

            $html .= '/>';
        }

        foreach ($document->getStyle() as $type => $content) {
            $html .= '<style type="'.$type.'">'.$content.'</style>';
        }

        return $html;
    }
}
