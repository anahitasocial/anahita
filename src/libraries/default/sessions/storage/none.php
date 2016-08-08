<?php

class LibSessionsStorageNone extends LibSessionsStorageAbstract
{
	/**
	* Register the functions of this class with PHP's session handler
	*
	* @access public
	* @param array $options optional parameters
	*/
	function register()
	{
		//let php handle the session storage
	}
}
