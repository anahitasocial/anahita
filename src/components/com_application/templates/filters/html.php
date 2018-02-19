<?php

/**
 * @category   Anahita
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationTemplateFilterHtml extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterWrite
{
    /**
     * Convert the alias.
     *
     * @param string
     *
     * @return LibBaseTemplateFilterAlias
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
        $html .= '<title>'.$document->getTitle().'</title>';
        $html .= '<meta name="description" content="'.$document->getDescription().'" />';

        /* Google+ Card*/
        $html .= '<meta itemprop="name" content="'.$document->getTitle().'" />';
        $html .= '<meta itemprop="description" content="'.$document->getDescription().'" />';

        if ($document->getImage()) {
            $html .= '<meta itemprop="image" content="'.$document->getImage().'" />';
        }

        /* Twitter Card */
        $twitter_card = ($document->getImage()) ? 'summary_large_image' : 'summary';
        $html .= '<meta name="twitter:card" value="'.$twitter_card.'">';
        $html .= '<meta property="twitter:title" content="'.$document->getTitle().'" />';
        $html .= '<meta property="twitter:description" content="'.$document->getDescription().'" />';
        $html .= '<meta property="og:type" content="'.$document->getType().'" />';
        $html .= '<meta property="twitter:site" content="'.$document->getLink().'" />';

        if ($document->getImage()) {
            $html .= '<meta property="twitter:image:src" content="'.$document->getImage().'" />';
        }


        /* Generic Open Graph tags */
        $html .= '<meta property="og:title" content="'.$document->getTitle().'" />';
        $html .= '<meta property="og:description" content="'.$document->getDescription().'" />';
        $html .= '<meta property="og:type" content="'.$document->getType().'" />';
        $html .= '<meta property="og:url" content="'.$document->getLink().'" />';

        if ($document->getImage()) {
            $html .= '<meta property="og:image" content="'.$document->getImage().'" />';
        }

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

        foreach ($scripts as $src => $script) {

            $type = $script['type'];

            $attribs = '';
            if (count($script['attribs'])) {
                $attribs = implode($script['attribs'], ' ');
            }

            $string .= '<script type="'.$type.'" src="'.$src.'" '.$attribs.'></script>';
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
