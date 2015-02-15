<?php
if ( defined('KOOWA') ) {
 
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Video content filter
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgContentfilterVideo extends PlgContentfilterAbstract
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
            'priority'   => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }
	
	/**
	 * Filter a value  
	 * 
	 * @param string The text to filter
	 * 
	 * @return string
	 */	
	public function filter($text)
	{
		$this->_stripTags($text);
		$this->_youtube($text);
		$this->_vimeo($text);
		$this->_replaceTags($text);
		return $text;
	}

	/**
	 * Checks the text for a vimeo URL 
	 * 
	 * @param string $text The text to filter
	 * 
	 * @return string
	 */
	protected function _vimeo(&$text)
	{
		$matches = array();
		
		if(preg_match_all('%http[s]?://\S*vimeo.com/(\d+)%', $text, $matches)) 
		{
			foreach($matches[1] as $index => $video_id) {				
				$url = JURI::base().'plugins/contentfilter/video.php?type=vimeo&id='.$video_id;				
				$options = array(
					'id'				=> $video_id,
					'url' 		  		=> 'https://vimeo.com/'.$video_id,
					'thumbnail'   		=> $url,
				    'type'				=> 'vimeo'
				);
				
				$video = $this->_createVideo($options);
				$text = str_replace($matches[0][$index], $video, $text);
			}
		}
	}	
	
	/**
	 * Checks the text for a youtube URL 
	 * 
	 * @param string $text The text to filter
	 * 
	 * @return string
	 */
	protected function _youtube(&$text)
	{
		$matches = array();

		if(preg_match_all('%http[s]?://?:\S+\.swf\b|\S+?youtu\.?be\S*\/(\S+)%', $text, $matches))
		{			
			foreach($matches[1] as $index => $match)
			{
				$youtube_link = $match;
				$full_link	  = $matches[0][$index];
				$id	  = array();
				$pattern = '/v=([^&#]+)/';
				if ( strpos($full_link,'.be/') ) $pattern = '/([^&#]+)/';
						
				if ( preg_match($pattern, $youtube_link, $id) )
				{
					$id   = str_replace('watch?v=','', array_pop($id));
		
					$options = array(						
						'id'		=> $id,
						'url' 		=> 'https://www.youtube.com/watch?v='.$id,
						'thumbnail' => 'https://img.youtube.com/vi/'.$id.'/0.jpg',
					    'type'		=> 'youtube'
					);
				
					$video = $this->_createVideo($options);
					$text = str_replace($matches[0][$index], $video, $text);
				}
			}
		}	
		
	}	
	
	/**
	 * Return a HTML tag for the video to be displayed. 
	 * 
	 * @param array $options The options to be passed, it must contait the video URL, video thumbnail and width and height
	 * 
	 * @return string
	 */
	protected function _createVideo(array $options)
	{
		$thumbnail = $options['thumbnail'];
		$url = $options['url'];
		$type = $options['type'];
		$id = $options['id'];

		return '<a data-trigger="MediaViewer" class="an-media-video-thumbnail" rel="'.$type.' '.$id.'" class="swipebox-video" href="'.$url.'">'.					
					'<img src="'.$thumbnail.'" />'.					
			   '</a>';
	}
}

} else {
	
	$type 	= @$_GET['type'];
	$video	= @$_GET['id'];
	if ( $video ) {		
		$contents = file_get_contents('https://vimeo.com/api/v2/video/'.$video.'.php');
		$array = array_shift(@unserialize(trim($contents)));
		$url   = $array['thumbnail_large'];
		header("Content-type: image/jpeg");
		print file_get_contents($url);
	}
}