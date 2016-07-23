<?php

class ComPeopleSessionStorageNone extends ComPeopleSessionStorageAbstract
{
	/**
	* Register the functions of this class with PHP's session handler
	*
	* @access public
	* @param array $options optional parameters
	*/
	function register($options = array())
	{
		//let php handle the session storage
	}
}
