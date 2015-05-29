<?php
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
            'priority' => KCommand::PRIORITY_HIGH,
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

		$this->_youtube($text);
		$this->_vimeo($text);

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
			foreach($matches[1] as $index => $id) 
			{				
			    $video = json_decode(file_get_contents('https://vimeo.com/api/v2/video/'.$id.'.json'));
                $video = $video[0];
			    
			    if($video && $video->id)
			    {
    				$options = array(
    				    'title' => $video->title,
    					'url' => 'https://vimeo.com/'.$video->id,
    					'thumbnail' => $video->thumbnail_large
    				);
    				
    				$video = $this->_createVideo($options);
    				$text = str_replace($matches[0][$index], $video, $text);
			    }
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

		if(preg_match_all('%http[s]?://\S*(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $text, $matches))
		{
			  
                			
			foreach($matches[1] as $index => $id)
			{

                $video = file_get_contents('https://gdata.youtube.com/feeds/api/videos/'.$id.'?alt=json');
				$video = json_decode($video, true);

				$thumbBase = 'https://img.youtube.com/vi/'.$id.'/';

                $maxres = get_headers($thumbBase.'maxresdefault.jpg');
                $notfound = get_headers($thumbBase.'0.jpg');

				if($maxres[0] != 'HTTP/1.0 404 Not Found') 
				{
				    $thumbnail = $thumbBase.'maxresdefault.jpg';
                }    
				elseif($notfound[0] != 'HTTP/1.0 404 Not Found') 
				{
				    $thumbnail = $thumbBase.'0.jpg';
                }
				else 
				    return;

				$options = array(						
				    'title' => $video['entry']['title']['$t'],
					'url' => 'https://www.youtube.com/watch?v='.$id,
					'thumbnail' => $thumbnail
				);
			
				$video = $this->_createVideo( $options );
				$text = str_replace($matches[0][$index], $video, $text);
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
	    return '<div class="an-media-video">' 
	    .'<img src="'.$options['thumbnail'].'" />'
	    .'<a data-rel="media-'.uniqid().'" data-trigger="MediaViewer" class="an-media-video-thumbnail" '
		.' href="'.$options['url'].'"'
		.' title="'.htmlspecialchars($options['title'], ENT_QUOTES).'" >'
	    .'</a></div>';
	}
}