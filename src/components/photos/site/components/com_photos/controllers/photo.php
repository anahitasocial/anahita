<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Photo Controller
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosControllerPhoto extends ComMediumControllerDefault
{
    /**
     * The max upload limit
     * 
     * @var int
     */
    protected $_max_upload_limit;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_max_upload_limit = $config->max_upload_zie;
    }
        
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
            'max_upload_zie' => get_config_value('photos.uploadlimit',4)
        ));
    
        parent::_initialize($config);
    }
        
	/**
	 * Browse Photos
	 * 
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _actionBrowse($context)
	{		
		$this->getService('repos://site/photos.set');

		$photos =  parent::_actionBrowse($context);
		
		$photos->order('creationTime', 'DESC');
		
		if($this->exclude_set != '')
		{
			$set = $this->actor->sets->fetch(array('id'=>$this->exclude_set));	
			
			if(!empty($set))
			{
				$photo_ids = array();
				foreach($set->photos as $photo)
					$photo_ids[] = $photo->id;
				
				if(count($photo_ids))
					$photos->where('photo.id', '<>', $photo_ids);
			}
		}
		
		return $photos;
	}
	
	/**
	 * Method to upload and Add a photo
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _actionAdd($context)
	{		
		$data 			= $context->data;			
		$file    		= KRequest::get('files.file', 'raw');
		$content 		= @file_get_contents($file['tmp_name']);
		$filesize		= strlen($content);
		$uploadlimit 	=  $this->_max_upload_limit * 1024 * 1024; 
		
		$exif = (function_exists('exif_read_data')) ? @exif_read_data($file['tmp_name']) : array();
		
		if( $filesize == 0 ) {
		    throw new LibBaseControllerExceptionBadRequest('File is missing');			
		}
		
		if( $filesize > $uploadlimit ) {
		    throw new LibBaseControllerExceptionBadRequest('Exceed maximum size');			
		}
		
		$orientation = 0;
		
		if(!empty($exif) && isset($exif['Orientation']) ) 
			$orientation = $exif['Orientation'];		
		
		$data['portrait']  = array('data'=>$content,'rotation'=>$orientation,'mimetype'=>isset($file['type']) ? $file['type'] : null);				
		$photo = $this->actor->photos->addNew($data);	
		$photo->setExifData($exif);
		$photo->save();
		$this->setItem($photo);
		$this->getResponse()->status = KHttpResponse::CREATED;
        if ( $photo->body && preg_match('/\S/',$photo->body) )
            $context->append(array(                
                'story' => array('body'=>$photo->body)
            ));

		return $photo;
	}
}