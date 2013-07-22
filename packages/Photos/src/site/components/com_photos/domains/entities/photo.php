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
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Photo Entity
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosDomainEntityPhoto extends ComMediumDomainEntityMedium 
{
	/**
	 * Default Image
	 * 
	 */
	const DEFAULT_IMAGE 	= 'default.png';
	
	/**
	 * Photo Size Constants
	 */
	const SIZE_ORIGINAL		= 'original';
	const SIZE_LARGE 		= 'large';
	const SIZE_MEDIUM 		= 'medium';
	const SIZE_SMALL 		= 'small';
	const SIZE_THUMBNAIL 	= 'thumbnail';
	const SIZE_SQUARE 		= 'square';
	
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
			'attributes' 	=> array('mimetype'),
			'behaviors'		=> array(
				'portraitable',
				'hittable'
			),
			'relationships'	=> array(
				'sets' => array('through'=>'edge')
			)
		));
				
		parent::_initialize($config);
	}	
	
	/**
	 * Obtain the array of image file exif data as it was captured 
	 * while uploading the original image
	 * 
	 * @return array
	 */
	public function getExifData()
	{
		return $this->getValue('exif_data', array());
	}
	
	/**
	 * Set the image file exif data
	 * 
	 * @param  array strucutre of the exif data read from the file
	 * @return void
	 */
	public function setExifData($data = array())
	{
		$this->setValue('exif_data', $data);
		return $this;
	}
	
	/**
	 * Synchronizes the photo sets
	 * 
	 * @return unknown_type
	 */
	public function delete()
	{
		//keep the photos set to use
		//for _afterEntityDelete
		$this->__sets = $this->sets->fetchSet();
		parent::delete();
	}
    
    /**
     * Return the portrait file for a size. Override the Portriable behavior due 
     * to some legacy
     * 
     * @see LibBaseDomainBehaviorPortraitable
     * 
     * @return string
     */
    public function getPortraitFile($size)
    {        
        $filename = $this->filename;
        
        //remove the extension 
        $extension = JFile::getExt($filename);
        $name      = JFile:: stripExt($filename);
       
        $filename = $name.'_'.$size.'.'.$extension;
        
        return $filename;
    } 
    
    /**
     * Return the filename
     * 
     * @return string
     */
    public function getFilename()
    {  
        $filename = $this->get('filename');
        
        //@legacy. some of the file names weren't saved
        if ( empty($filename) && $this->persisted() ) {
            $filename = md5($this->getIdentityId()).'.jpg';
        }        
        
        return $filename;      
    }
	
    /**
     * Track the filename
     * 
     * KCommandContext $context Context
     * 
     * @see self::_afterEntityDelete
     * 
     * @return void
     */
    protected function _beforeEntityDelete(KCommandContext $context)
    {
        //we need the filename since when it's deleted the filename
        //is set to null
        $context->filename = $this->filename;
    }
        
	/**
	 * Delete the photo from all the sets
	 * 
	 * KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _afterEntityDelete(KCommandContext $context)
	{	    
		if ( !empty($this->__sets) ) 
        {            
			foreach($this->__sets as $set)
			{
				$count = $set->photos->getTotal();
				if( $count == 0 )
					$set->delete();
				else 
				{
					if( $set->coverFileIdentifier == $context->filename )
						$set->coverFileIdentifier = $set->photos->fetch()->filename;
				}
			}
		}
	}

	/**
	 * Before an image is stored for a photo
	 *
	 * @param  KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _beforeEntityStoreimage($context)
	{
		$image = $context->image;

		$sizes = array();
		
		$width  = imagesx($image);		
		$height = imagesy($image);

		$sizes[ComPhotosDomainEntityPhoto::SIZE_ORIGINAL] = $width.'x'.$height;
		
		if($width > 1024)
			$sizes[ComPhotosDomainEntityPhoto::SIZE_LARGE] = '1024x1024';
		
		if($width >= 640)
			$sizes[ComPhotosDomainEntityPhoto::SIZE_MEDIUM] = '640xauto';
		else
			$sizes[ComPhotosDomainEntityPhoto::SIZE_MEDIUM] = $sizes[ComPhotosDomainEntityPhoto::SIZE_ORIGINAL];
		
		if($width >= 240)	
			$sizes[ComPhotosDomainEntityPhoto::SIZE_SMALL] = '240x240';
		else
			$sizes[ComPhotosDomainEntityPhoto::SIZE_SMALL] = $sizes[ComPhotosDomainEntityPhoto::SIZE_ORIGINAL];
		
		$sizes[ComPhotosDomainEntityPhoto::SIZE_THUMBNAIL] = '100x100';
		$sizes[ComPhotosDomainEntityPhoto::SIZE_SQUARE] = '100';
		 
		$context->sizes = $sizes;
	}
}