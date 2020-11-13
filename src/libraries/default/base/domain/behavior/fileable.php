<?php

/**
 * Fileable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2020 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorFileable extends LibBaseDomainBehaviorStorable
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'filename' => array (
                    'column' => 'filename',
                    'type' => 'string',
                ),
                'filesize' => array(
                    'column' => 'filesize',
                    'type' => 'integer',
                ),
                'mimetype' => array(
                    'column' => 'mimetype',
                    'match' => '/\w+\/\w+/',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Store Data.
     *
     * @param array|AnConfig $file
     */
    public function storeFile($file)
    {
        $filename = md5($this->id);
        $data = file_get_contents($file->tmp_name);

        if ($this->getFileName() == $this->name) {
            $this->name = $file->name;
        }

        $file->append(array(
            'type' => mime_content_type($file->name),
        ));

        $this->mimetype = $file->type;
        $this->filename = $file->name;
        $this->fileSize = strlen($data);
        $this->writeData($filename, $data, false);
    }
    
    public function setFileContent($file)
	{
        if (! $this->validate() ) {
			throw new KException('Something bad happened');
		}
        		
		if (! $this->persisted()) {	
            $settings = $this->getService('com:settings.config');
            
            $mimetypes = include(ANPATH_COMPONENTS . DS . 'com_documents' . DS . 'mimetypes.php');
            $this->mimetype = $file['type'];
            $extension = array_search($this->mimetype, $mimetypes);
			$this->filename = hash('sha256', str_shuffle($settings->secret . microtime())) . '.' . $extension;
			
            
            $data = file_get_contents($file['tmp_name']);
            $this->filesize = strlen($data);
            
            $this->save();		
            	
			$this->writeData($this->filename, $data, false);
		}
	}
    
    /**
	 * Return the file content;
	 * 
	 * @return string
	 */
	public function getFileContent()
	{
		return $this->readData($this->filename, false);		
	}
    
    public function getFileExtension()
    {
        $mimetypes = include(ANPATH_COMPONENTS . DS . 'com_documents' . DS . 'mimetypes.php');
        $extension = array_search($this->mimetype, $mimetypes);
        
        return $extension;
    }
    
    /**
	 * Delete a photo image from the storage. 
	 * 
	 * @param AnCommandContext $context Context parameter
	 */
	protected function _beforeEntityDelete(AnCommandContext $context)
	{
		$this->deletePath($this->filename, false);
	}
}
