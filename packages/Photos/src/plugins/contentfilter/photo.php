<?php 

/**
 * LICENSE: ##LICENSE##
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Creates a hyperlink from the URLs
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
 
 class PlgContentfilterPhoto extends PlgContentfilterAbstract
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }
    
    /**
     * Filter a value. 
     * 
     * @param string The text to filter
     * 
     * @return string
     */ 
    public function filter($text)
    {
       
        preg_match_all('/photos\/([0-9]+)[-]?/', $text, $matches);
        
        $ids = $matches[1];

        foreach($ids as $id)
        {

           $id = (int) $id;     
           $photo = KService::get('repos:photos.photo')->getQuery()->disableChain()->id($id)->fetch();
                        
            if ( isset($photo->id) ) 
            {
                $caption = htmlspecialchars( $photo->title, ENT_QUOTES, 'UTF-8' );
                
                $pattern = '/((?<!=\")[http]+[s]?:\/\/[^<>\s]+)\/photos\/'.$photo->id.'[-\w\-]*/';
                
                $text = preg_replace( $pattern,
                '<a data-trigger="MediaViewer" href="'.$photo->getPortraitURL('original').'" title="'.$caption.'" >'
                .'<img alt="'.$caption.'" src="'.$photo->getPortraitURL('medium').'" />'
                .'</a> ', 
                $text );
            }
        }
        
        

        return $text;   
    }       
}    
