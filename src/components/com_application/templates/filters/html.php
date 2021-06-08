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
            $text = str_replace('<html', '<html lang="en"', $text);

            //render the styles
            $text = str_replace('</head>', $this->_renderHead() . '</head>', $text);
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
        
        if ($document->getTitle()) {
            $html .= '<title>'.$document->getTitle().'</title>';
        }
        
        $html .= '<meta name="description" content="'.$document->getDescription().'" />';

        /* Twitter Card */
        $twitter_card = ($document->getImage()) ? 'summary_large_image' : 'summary';
        $html .= '<meta name="twitter:card" value="'.$twitter_card.'">';
        
        if ($document->getTitle()) {
            $html .= '<meta property="twitter:title" content="'.$document->getTitle().'" />';
            $html .= '<meta property="og:title" content="'.$document->getTitle().'" />';
        }
        
        if ($document->getDescription()) {
            $html .= '<meta property="twitter:description" content="'.$document->getDescription().'" />';
            $html .= '<meta property="og:description" content="'.$document->getDescription().'" />';
        }
        
        if ($document->getType()) {
            $html .= '<meta property="og:type" content="'.$document->getType().'" />';
            $html .= '<meta property="og:type" content="'.$document->getType().'" />';
        }
        
        if ($document->getLink()) {
            $settings = $this->getService('com:settings.config');
            $path = parse_url($document->getLink(), PHP_URL_PATH);

            $html .= '<meta property="twitter:site" content="'.$settings->client_domain.$path.'" />';
            $html .= '<meta property="og:url" content="'.$settings->client_domain.$path.'" />';
        }
        
        if ($document->getImage()) {
            $html .= '<meta property="twitter:image:src" content="'.$document->getImage().'" />';
            $html .= '<meta property="og:image" content="'.$document->getImage().'" />';
        }

        return $html;
    }
}
