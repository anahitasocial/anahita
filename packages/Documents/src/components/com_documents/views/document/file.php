<?php

class ComDocumentsViewDocumentFile extends LibBaseViewFile
{
	public function display()
	{
		$this->output   = $this->_state->getItem()->getFileContent();
		$this->filename	= $this->_state->getItem()->filename;
		$this->mimetype = $this->_state->getItem()->mimetype;
		$this->disposition = 'attachment';	
        	
		return parent::display();		
	}
}