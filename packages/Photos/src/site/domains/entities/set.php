<?php

/** 
 * LICENSE: ##LICENSE##
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

jimport('joomla.filesystem.file');

/**
 * Set Entity
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosDomainEntitySet extends ComMediumDomainEntityMedium 
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
			'attributes' 	=> array(
				'name'				   => array('required'=>true),
				'coverFileIdentifier'  => 'filename'
			),
			'behaviors'		=> array(				
				'hittable'
			),			
			'relationships'	=> array(
				'photos' => array('through'=>'edge')
			)
		));
					
		parent::_initialize($config);		
	}
	
	/**
	 * Obtains the image file source
	 * 
	 * @return string path to image source 
	 * @param $size photo size. One of the constan sizes in the ComPhotosDomainEntityPhoto class
	 */
	public function getCoverSource($size=ComPhotosDomainEntityPhoto::SIZE_SQUARE)
	{
		if($this->hasCover())         
        {
            $filename  = $this->coverFileIdentifier;
            //remove the extension 
            $extension = JFile::getExt($filename);
            $name      = JFile:: stripExt($filename);
            $filename = $name.'_'.$size.'.'.$extension;                               
			return $this->owner->getPathURL('com_photos/'.$filename);
        }
		
		return 'base://media/com_photos/images/default.png';		
	}
	
	/**
	 * Adds a photo to an set
	 * 
	 * @return true on success
	 * @param $photo a ComPhotosDomainEntityPhoto object
	 */
	public function addPhoto($photo)
	{
		$photos = AnHelperArray::getIterator($photo);
		
		foreach($photos as $photo) 
		{
			if ( !$this->photos->find($photo) ) 
			{
			    $this->photos->insert($photo, array(
			         'author'=> $photo->author
                ));
			}
		}

		if(!$this->hasCover())
		{
			$cover = is_array($photos) ? $photos[0] : $photos->top();
			$this->setCover($cover);
		}
	}
	
	/**
	 * Removes a photo or list of photos from the set
	 * 
	 * @return null
	 * @param $photo a ComPhotosDomainEntityPhoto object
	 */
	public function removePhoto($photo)
	{
		$photos = AnHelperArray::getIterator($photo);
		
		foreach($photos as $photo) {
			if ( $edge = $this->photos->find($photo) )
				$edge->delete();
				
			if( $this->isCover($photo) )
				$this->coverFileIdentifier = null;
		}
	}
	
	/**
	 * Orders the photos in this set
	 * 
	 * @param array $photo_ids
	 */
	public function reorder($photo_ids)
	{		
		if(count($photo_ids) == 1)
		{
			if($edge = $this->getService('repos://site/photos.edge')->fetch(array('set'=>$this,'photo.id'=>$photo_ids[0])))
				$edge->ordering = $this->photos->getTotal();
				
			return;
		}
		
		foreach($photo_ids as $index=>$photo_id)
		{
			if($edge = $this->getService('repos://site/photos.edge')->fetch(array('set'=>$this,'photo.id'=>$photo_id)))
				$edge->ordering = $index + 1;
		}
	}
	
	/**
	 * Return true or false if the set has a cover
	 * 
	 * @return boolean
	 */
	public function hasCover()
	{
		return strlen($this->coverFileIdentifier) > 0;
	}
	
	/**
	 * Determines if the given photo is the set cover or not.
	 * 
	 * @param $photo a ComPhotosDomainEntityPhoto object
	 * @return boolean
	 */
	public function isCover($photo)
	{
		return $this->coverFileIdentifier == $photo->filename;
	}
	
	/**
	 * Sets the set cover image
	 * 
	 * @return null
	 * @param $photo a ComPhotosDomainEntityPhoto object
	 */
	public function setCover($photo)
	{
		$this->coverFileIdentifier = $photo->filename;
	}
	
	/**
	 * Gets number of photos in this set
	 * 
	 * @return integer value
	 */
	public function getPhotoCount()
	{
		return $this->getValue('photo_count', 0);
	}
}