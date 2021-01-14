<?php

class ComDocumentsViewDocumentFile extends LibBaseViewFile
{
	public function display()
	{
		$this->output = $this->_state->getItem()->getFileContent();
		$extension = $this->_state->getItem()->getFileExtension();
		
		$this->filename	= $this->_state->getItem()->alias . '.' . $extension;
		
		$this->mimetype = $this->_state->getItem()->mimetype;
		$this->disposition = 'inline';	
        	
		return parent::display();		
	}
}