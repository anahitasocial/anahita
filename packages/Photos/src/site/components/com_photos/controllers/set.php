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
 * Album Controller
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosControllerSet extends ComMediumControllerDefault
{	 	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
				
		$this->registerCallback(
			array('before.browse', 'before.read', 'before.add', 'before.addphoto', 'before.removephoto', 'before.updatephotos', 'before.updatecover' ), 
			array($this, 'fetchPhoto'));
			
		$this->registerCallback(
			array('after.addphoto', 'after.removephoto', 'after.updatephotos'), 
			array($this, 'reorder'));
	}
	
	/**
	 * Browse Albums
	 * 
	 * @return void;
	 */
	protected function _actionBrowse($context)
	{					
        $sets =  parent::_actionBrowse($context);
        
      	$sets->order('updateTime', 'DESC');
        
        if($this->photo_id && $this->getRequest()->get('layout') != 'selector')
        	$sets->where('photos.id', '=', $this->photo_id);
		
		return $sets;
	}
	
	/**
	 * Updates the photos in a set given an array of ids
	 * 
	 * @param object POST data 
	 * @return object ComPhotosDomainEntitySet
	 */
	protected function _actionUpdatephotos($context)
	{	
		$this->execute('addphoto', $context);
		
		$photo_ids = (array) KConfig::unbox( $context->data->photo_id );
		
		foreach($this->getItem()->photos as $photo)
			if(!in_array($photo->id, $photo_ids))
				$this->getItem()->removePhoto($photo);
				
		if(!$this->getItem()->hasCover())
			$this->execute('updatecover', $context);		
				
		return $this->getItem();
	}
	
	/**
	 * Reorders the photos in a set in respect with the order of ids
	 * 
	 * @param object POST data
	 * @return object ComPhotosDomainEntitySet
	 */
	protected function _actionReorder($context)
	{
		$photo_ids = (array) KConfig::unbox( $context->data->photo_id );
		
		$this->getItem()->reorder($photo_ids);

		return $this->getItem();
	}
	
	/**
	 * Adds a photos to an set
	 * 
	 * @return object ComPhotosDomainEntitySet
	 * @param object POST data
	 */
	protected function _actionAddphoto($context)
	{	
		$this->getItem()->addPhoto($this->photo);
		
		$context->response->setRedirect(JRoute::_($this->getItem()->getURL()));
		
		return $this->getItem();
	}
	
	/**
	 * Removes a list of photos from an set
	 * 
	 * @return object ComPhotosDomainEntitySet
	 * @param object POST data
	 */
	protected function _actionRemovephoto($context)
	{		
        $lastPhoto = ($this->getItem()->photos->getTotal() > 1) ? false : true;
        
		$this->getItem()->removePhoto($this->photo);
        
		if($lastPhoto)
		{
			$this->getResponse()->status = 204;
			return;
		}
		else
			return $this->getItem();
	}
	
	/**
	 * Updates the set cover
	 * 
	 * @return object ComPhotosDomainEntitySet
	 * @param object POST data
	 */
	protected function _actionUpdatecover($context)
	{	
		$this->getItem()->setCover($this->photos->top());
		return $this->getItem();
	}
	
	/**
	 * Fetches a photo object given photo_id as a GET request
	 * 
	 * @return null
	 * @param object POST data
	 */
	public function fetchPhoto(KCommandContext $context)
	{			
		$data = $context->data;
        
		$data->append(array(
			'photo_id' => $this->photo_id
		));
			
		$photo_id = (array) KConfig::unbox( $data->photo_id );	
        	   
		if ( !empty($photo_id) ) 
        {
			$photo = $this->actor->photos->fetchSet(array('id'=>$photo_id));
            					
			if ( count($photo) === 0 )
				$photo = null;
                
            $this->photos = $this->photo = $photo;
		}
		
		return $this->photo;
	}
	
	/**
	 * Fetches an entity
	 * 
	 * @return null
	 * @param object POST data
	 */	
	public function fetchEntity(KCommandContext $context)
	{
		if ( $context->action == 'addphoto' )
		{		
			if ( $context->data->id )
				$this->id = $context->data->id;
			//clone the context so it's not touched
			$set = $this->__call('fetchEntity', array($context));
			
			if ( !$set )
			{
				$context->setError(null);
				//if the action is addphoto and there are no sets then create an set
				$set = $this->add($context);
			}
				
			return $set;
		}
		else 
			return $this->__call('fetchEntity', array($context));
	}

//end class	
}