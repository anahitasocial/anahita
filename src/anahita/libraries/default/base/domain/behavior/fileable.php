<?php

/** 
 * LICENSE: ##LICENSE##
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

/**
 * Fileable Behavior
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorFileable extends LibBaseDomainBehaviorStorable 
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
				'filesize'		   => array('column'=>'medium_excerpt',   'type'=>'integer', 	 'write'=>'private'),
				'mimeType'		   => array('column'=>'medium_mime_type', 'match'=>'/\w+\/\w+/', 'write'=>'private'),
			)
		));	
		
		parent::_initialize($config);	
	}
	
	/**
	 * Store Data
	 *
	 * @param   array|KConfig $file
	 * @return  void
	 */
	public function storeFile($file)
	{		
		$filename = md5($this->id);
		$data	  = file_get_contents($file->tmp_name);		
		if ( $this->getFileName() == $this->name ) {
			$this->name = $file->name;
		}
		$file->append(array(
			'type' => mime_content_type($file->name)
		));
		$this->mimeType = $file->type;
		$this->setValue('file_name', $file->name);
		$this->fileSize = strlen($data);
		$this->writeData($filename, $data, false);
	}
		
	/**
	 * Return the file content;
	 * 
	 * @return string
	 */
	public function getFileContent()
	{
		$filename = md5($this->id);
		return $this->readData($filename, false);		
	}
	
	/**
	 * Return the original file name
	 * 
	 * @return string
	 */
	public function getFileName()
	{
		return $this->getValue('file_name');
	}	
	
}