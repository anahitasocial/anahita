<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.filesystem.file');

/**
 * Portraitable Behavior. 
 * 
 * An image representation of a node
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorPortraitable extends LibBaseDomainBehaviorStorable 
{
	/**
	 * Return an array of avatar sizes with its respective dimension
	 *  
	 * @return array 
	 */		
	static public function getDefaultSizes()
	{
		return array('small'=>'80xauto', 'medium' => '160xauto', 'large' => '480xauto', 'square' => 56);
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
			'attributes' 	=> array(
				'filename' => array('write'=>'protected'),
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Persist the data of a photo into the storage 
	 * 
	 * @param array $options An array of options
	 * 
	 * @return boolean
	 */
	public function setPortraitImage($config = array())
	{				
		$config = new KConfig($config);
		
		$config->append(array(
			'rotation' => 0,	
			'mimetype' => 'image/jpeg'
		));
        
		if ( $config->url ) {
			$config->append(array(
				'data' => file_get_contents($config->url)
			));
		}
        
        $config->mimetype = strtolower($config->mimetype);
        
        //the allowed mimetypes
        $mimetypes = array('image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif');
        
        //force mimetype to jpeg if invalid
        //@TODO is this wise ??
        if ( !isset($mimetypes[$config->mimetype]) ) {
            $config->mimetype = 'image/jpeg';   
        }
        
        //first remove the existing avatar. 
        //only remove exisitng if the entity hasn't been
        //just inserted
        if ( $this->state() != AnDomain::STATE_INSERTED && 
        	 $this->state() != AnDomain::STATE_NEW ) {            
            //remove existing portrait image
            $this->removePortraitImage();            
        }

        if ( $config->data ) {
        	
        	$data = $config->data;
        	
        	//if data is null or mimetype is invalid then
        	//existing avatar is deleted
        	if ( empty($data) ) {
        		if ( $this->state() == AnDomain::STATE_NEW) {
        			$this->reset();
        		}
        		return false;
        	}
        	
        	$config->append(array(
        		'image' => 	imagecreatefromstring($data)
        	));        	
        }
		
        $image = $config->image;
				
		if ( empty($image) ) {
			if ( $this->state() == AnDomain::STATE_NEW) {
				$this->reset();
			}
			return false;	
		}	
		
		if ( $this->state() == AnDomain::STATE_NEW ) {
			$config['image'] = $image;
			unset($config['data']);
			unset($config['url']);
			$this->__image_options = $config;
			return $this;
		}
		
		$rotation  = $config->rotation;
		
		switch($rotation)
		{
			case 3:$rotation=180;break;
			case 6:$rotation=-90;break;
			case 8:$rotation=90 ;break;
			default :
				$rotation = 0;
		}
		
		if($rotation != 0 ) {
			$image = imagerotate($image, $rotation, 0);
		}		
                    
        $extension = $mimetypes[$config->mimetype];
        
        $filename  = md5(time()).'.'.$extension;
        
		$context = new KCommandContext();
		
		$context->append(array( 
			'image' => $image,			
			'sizes' => array()
		));
        
		$this->_mixer->execute('before.storeimage', $context);

        $sizes   = KConfig::unbox($context->sizes);
        
        if ( empty($sizes) ) {
            $sizes = self::getDefaultSizes();
        }
        
		$this->_mixer->setData(array(
			'filename'  => $filename,
		), AnDomain::ACCESS_PROTECTED);
		                
		foreach($sizes as $size => $dimension )
		{
            $data = AnHelperImage::resize($image, $dimension);
            $data = AnHelperImage::output($data, $config->mimetype);
			$filename = $this->_mixer->getPortraitFile($size);
			$this->_mixer->writeData($filename, $data);
		}
		
		$this->_mixer->setValue('sizes', $sizes);
		
		imagedestroy($image);
        unset($this->__image_options);        
		return $this->_mixer;
	}

	/**
	 * Return if the portrait is set
	 * 
	 * @return boolean
	 */
	public function portraitSet()
	{
		return !empty($this->filename);
	}
	
	/**
	 * Removes the portrait image
	 * 
	 * @return void
	 */
	public function removePortraitImage()
	{
		$sizes   = $this->_mixer->getPortraitSizes();
        
		if ( empty($sizes) ) {
			$sizes = explode(' ','original large medium small thumbnail square');
		} else
			$sizes = array_keys($sizes);
				
		foreach($sizes as $size) {
			$file = $this->_mixer->getPortraitFile($size);
			$this->_mixer->deletePath($file);
		}
		$this->filename = null;
	}	
	
    /**
     * Return the portrait file for a size
     * 
     * @return string
     */
    public function getPortraitFile($size)
    {
        //returns [SIZE].[Filename].[extension];
        return $size.$this->filename;
    }
	
	/**
	 * Return the URL to the portrait
	 * 
	 * @return string
	 */
	public function getPortraitURL($size='square')
	{
		$filename =  $this->_mixer->getPortraitFile($size);
		$url = $this->getPathURL($filename, true);
		return $url;
	}
		
	/**
	 * Obtain the list of available sizes and dimensions for this photo
	 * 
	 * @return array of $size=>$dimension
	 */
	public function getPortraitSizes()
	{
		return $this->getValue('sizes', array());
	}
		
	/**
	 * Called after inserting the entity
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _afterEntityInsert(KCommandContext $context)
	{
		if ( !empty($this->__image_options) ) {
			$this->_mixer->setPortraitImage($this->__image_options);
        }
	}
	
	/**
	 * Delete a photo image from the storage. 
	 * 
	 * @param KCommandContext $context Context parameter
	 *  
	 * @return boolean
	 */
	protected function _beforeEntityDelete(KCommandContext $context)
	{
		$this->_mixer->removePortraitImage();
	}
}