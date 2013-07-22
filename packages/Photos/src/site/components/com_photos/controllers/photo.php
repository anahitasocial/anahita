<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
		$filesize		= $file['size'];
		$content 		= @file_get_contents($file['tmp_name']);
		
		$uploadlimit 	= get_config_value('photos.uploadlimit',4) * 1024 * 1024; 
		
		$exif = (function_exists('exif_read_data')) ? @exif_read_data($file['tmp_name']) : array();
		
		if(strlen($content) == 0)
			return false;
		
		if($filesize > $uploadlimit)
		{
			$context->setError(new KControllerException('Maximum file size exceeded!', KHttpResponse::NOT_ACCEPTABLE));
			return false;
		}
		
		$orientation = 0;
		if(!empty($exif) && isset($exif['Orientation']) )
			$orientation = $exif['Orientation'];
		
		$photo = $this->actor->photos->create();		
        $ret   = $photo->setPortraitImage(array(
                'data'     => $content, 
                'rotation' => $orientation,
                'mimetype' => $file['type']
            ));
        
        if ( $ret === false ) {
        	return false;
        }
        
		$photo->setExifData($exif);
        
        $this->setItem($photo);
        
        unset($data['name']);
        
        $photo->setData($data);
        
        if ( $photo->body && preg_match('/\S/',$photo->body) )
            $context->append(array(                
                'story' => array('body'=>$photo->body)
            ));

		return $photo;
	}
}