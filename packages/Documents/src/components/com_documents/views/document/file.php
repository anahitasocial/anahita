<?

class ComVchViewDocumentFile extends LibBaseViewFile
{
	public function display()
	{
		$this->output   = $this->_state->getItem()->getFileContent();
		$this->filename	= $this->_state->getItem()->name;
		$this->mimetype = $this->_state->getItem()->mimetype;
		$this->disposition = 'inline';	
        	
		return parent::display();		
	}
}