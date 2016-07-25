<?php

class LibSessionStorageNone extends LibSessionStorageAbstract
{
	/**
	* Register the functions of this class with PHP's session handler
	*
	* @access public
	* @param array $options optional parameters
	*/
	function register(KConfig $config)
	{
		//let php handle the session storage
	}
}
