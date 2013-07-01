<?php defined('KOOWA') or die('Restricted access');
/**
 * @version		$Id
 * @category	Anahita
 * @package		Anahita_Social_Applications
 * @subpackage	Photos
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.cache.cache' );


class plgContentAn_Photos extends JPlugin
{
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;
		
		// simple performance check to determine whether bot should process further
    	if ( strpos( $article->text, '{an-photos' ) === false ) {
    		return true;
    	}
    	
    	// define the regular expression for the bot
    	$regex = "#{an-photos(.*?)}#s";
    	
    	// find all instances of plugin and put in $matches
    	preg_match_all( "#{an-photos(.*?)}#s", $article->text, $matches );
    	
    	// Number of plugins
     	$count = count( $matches[0] );
     	
     	if($count)
     		$this->plgContentDisplayImages( $article, $matches, $count );
     		
     	return true;
	}
	
	function plgContentDisplayImages( &$article, &$matches, $count ) 
	{
		JHTML::script('lib_anahita/js/vendors/mediabox.js', 'media/', false);
		
		$app = JFactory::getApplication();		

		for ( $i=0; $i < $count; $i++ )
    	{
    		if (@$matches[1][$i]) 
    		{
    			$inline_params = $matches[1][$i];
    			
    			// get set_id
        		$set_matches = array();
        		$set_id = null;
        		preg_match( '# set_id="(.*?)"#s', $inline_params, $set_matches );
        		if (isset($set_matches[1])) $set_id = (int) trim($set_matches[1]);
    		}
    		
    		if($set_id)
    		{
    			//load set object
    			$set = KService::get('repos:photos.set')->getQuery()->disableChain()->id($set_id)->fetch();
    			
    			if($set)
    			{
    				$do_cache = $this->params->get('cache', true);
    				if ( $do_cache )
    				{
	    				$cache = JFactory::getCache('plg_an_photos');
	    				$cache->setLifeTime( $app->getCfg('cachetime') * 100); //cache one day
	    				$text = $cache->get(array($this, 'getSetHtml'), array($set), md5($set->id));    					
    				} else {
    					$text = $this->getSetHtml($set);
    				}

    			}
    			else 	
    				$text = '<div class="alert alert-error">'.JText::_('LIB-AN-NO-RECORD-AVAILABLE').'</div>';		
    			    			
    			$article->text = str_replace( $matches[0][$i], $text, $article->text );	
    		}
    	}
	}
	
	function getSetHtml($set)
	{
	    if($set->id )
	    {
	        $photos = $set->photos->getQuery()->order('photoSets.ordering')->disableChain()->fetchSet();
	        
	        if ( count($photos) === 0 )
	            return;
	        
			$text   = '<div class="an-photos-plugin">';
	    	$text  .= '<div class="media-grid" data-behavior="Mediabox">';						
			
			foreach($photos as $photo)
			{	
				$caption = htmlspecialchars($photo->title, ENT_QUOTES).
				(($photo->title && $photo->description) ? ' :: ' : '').
                
				KService::get('com://site/base.template.helper.text')->script($photo->description);
				
				$text .= '<div>';
				$text .= '<a rel="lightbox[set-'.$set->id.' 900 900]" title="'.$caption.'" href="'.$photo->getPortraitURL('medium').'">';
				$text .= '<img alt="'.htmlspecialchars($photo->title, ENT_QUOTES).'" class="set thumbnail" src="'.$photo->getPortraitURL('square').'" />';
				$text .= '</a>';
				$text .= '</div>';
			}	
			
			$text .= '</div></div>';
	
			return $text;
	    }
	    
	    return '';
	}
	
//end class	
}



