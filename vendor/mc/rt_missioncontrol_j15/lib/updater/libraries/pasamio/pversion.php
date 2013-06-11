<?php

RokUpdater::import('joomla.version');

class PVersion extends JVersion 
{
	/**
	 * Returns the user agent.
	 * 
	 * @param	string	Name of the component.
	 * @param	bool	Mask as Mozilla/5.0 or not.
	 * @param	bool	Add version afterwards to component.
	 * @return	string	User Agent.
	 */
	public function getUserAgent($component = null, $mask = false, $add_version = true)
	{
		if ($component === null) {
			$component = 'Framework';	
		}
		
		if ($add_version) {
			$component .= '/'.$this->RELEASE;	
		}
		
		// If masked pretend to look like Mozilla 5.0 but still identify ourselves.
		if ($mask) {
			return 'Mozilla/5.0 '. $this->PRODUCT .'/'. $this->RELEASE . '.'.$this->DEV_LEVEL . ($component ? ' '. $component : '');	
		}
		else {
			return $this->PRODUCT .'/'. $this->RELEASE . '.'.$this->DEV_LEVEL . ($component ? ' '. $component : '');
		}
	}
}