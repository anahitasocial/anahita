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

        if(strpos($text, '<html'))
        {
            //add language
            $text = str_replace('<html', '<html lang="'.JFactory::getLanguage()->getTag().'"', $text);

            //render the styles
            $text = str_replace('</head>', $this->_renderHead().$this->_renderStyles().'</head>', $text);

            //render the scripts                
            $text = str_replace('</body>', $this->_renderScripts().'</body>', $text);
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
        $html = '<base href="base://" />';
        
        $html .= '<meta name="description" content="'.$document->getDescription().'" />';
        
        if(isset($document->_custom))
        	foreach($document->_custom as $custom)
            	$html .= $custom;       
        
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
        
        //include tranlsation files
        $string .= $this->_template->getHelper('javascript')->language('lib_anahita');
        
        // Generate script file links
        $scripts = array_reverse($document->_scripts);
        
        foreach($scripts as $src => $type)
            $string .= '<script type="'.$type.'" src="'.$src.'"></script>';    
            
        // Generate script declarations
        $script = $document->_script;
        
        foreach($script as $type => $content)
            $string .= '<script type="'.$type.'">'.$content.'</script>';    
            
        return $string;         
    }
   
    /**
     * Return the document styles
     * 
     * @return string
     */
    protected function _renderStyles()
    {
        $document = JFactory::getDocument();
        $html = '';
                
        // Generate stylesheet links
        foreach($document->_styleSheets as $src => $attr)
        {
            $rel = 'stylesheet';
            
            if(strpos($src, '.less'))
                $rel .= '/less';
            
            $html .= '<link rel="'.$rel.'" href="'.$src.'" type="'.$attr['mime'].'"';
            
            if(isset($attr['media']))
                $html .= ' media="'.$attr['media'].'" ';
            
            if($temp = JArrayHelper::toString($attr['attribs']))
                $html .= ' '.$temp;
            
            $html .= '/>';
        }
        
        foreach($document->_style as $type => $content)
        	$html .= '<style type="'.$type.'">'.$content.'</style>';    
           
        return $html; 
    }       
}