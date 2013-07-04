<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * 
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibApplicationTemplateFilterHtml extends KTemplateFilterAbstract implements KTemplateFilterWrite
{   
    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function write(&$text) 
    {       
        $matches  = array();

        if ( strpos($text, '<html') )
        {
            //add language
            $text = str_replace('<html', '<html lang="'.JFactory::getLanguage()->getTag().'"', $text);

            //render the head
            $text = str_replace('<head>', '<head>'.$this->_renderHead(), $text);

            //render the styles
            $text = str_replace('</head>',$this->_renderStyles().'</head>', $text);

            //render the scripts                
            $text = str_replace('</body>',$this->_renderScripts().'</body>', $text);
        }
    }
    
    /**
     * Render title
     * 
     * @return string
     */
    protected function _renderHead()
    {
        $document = JFactory::getDocument();
        $html     = '<base href="base://" />';
        foreach ($document->_metaTags as $type => $tag)
        {
            foreach ($tag as $name => $content)
            {
                if ($type == 'http-equiv') {
                    $html .= '<meta http-equiv="'.$name.'" content="'.$content.'"'.'/>';
                } 
                
                elseif ($type == 'standard') {
                    $html .= '<meta name="'.$name.'" content="'.str_replace('"',"'",$content).'"'.'/>';
                }
            }
        }
        
        $html .= '<meta name="description" content="'.$document->getDescription().'" />';
        $html .= '<meta name="generator" content="'.$document->getGenerator().'" />';
        
        if ( isset($document->_custom) )
        {
            foreach($document->_custom as $custom) {
                $html .= $custom;
            }            
        }        
        
        $html .= '<title>'.$document->getTitle().'</title>';
        
        return $html;
    }
    
    
    /**
     * Return the document scripts
     * 
     * @return string
     */
    protected function _renderScripts()
    {
        $document = JFactory::getDocument();
        $string = '';
        
        // Generate script file links
        foreach ($document->_scripts as $src => $type) {
            $string .= '<script type="'.$type.'" src="'.$src.'"></script>';
        }
        
        // Generate script declarations
        foreach ($document->_script as $type => $content) {
            $string .= '<script type="'.$type.'">'.$content.'</script>';
        }
        
        $string .= $this->_template->getHelper('javascript')->language('lib_anahita');
                
        return   $string;         
    }
   
    /**
     * Return the document styles
     * 
     * @return string
     */
    protected function _renderStyles()
    {
        $document = JFactory::getDocument();
        $html     = '';
                
        // Generate stylesheet links
        foreach ($document->_styleSheets as $src => $attr )
        {
            $rel    = 'stylesheet';
            
            if ( strpos($src, '.less') ) {
                $rel .= '/less';
            }
            
            $html .= '<link rel="'.$rel.'" href="'.$src.'" type="'.$attr['mime'].'"';
            
            if ( isset($attr['media']) ) {
                $html .= ' media="'.$attr['media'].'" ';
            }
            
            if ($temp = JArrayHelper::toString($attr['attribs'])) {
                $html .= ' '.$temp;;
            }
            
            $html .= '/>';
        }
        
        foreach ($document->_style as $type => $content) {
            $html .= '<style type="'.$type.'">'.$content.'</style>';
        }         
           
        return $html; 
    }       
}