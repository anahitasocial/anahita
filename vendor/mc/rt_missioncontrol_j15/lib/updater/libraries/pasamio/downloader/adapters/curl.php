<?php

RokUpdater::import('joomla.base.adapterinstance');

class DownloadercURL extends JAdapterInstance {
	var $_filename = '';

	function header_callback($ch, $header) {
		$filename =& curl_filename();
		$parts = explode("\n", $header);
		foreach($parts as $wrapper_data) {
			if (substr($wrapper_data, 0, strlen("Content-Disposition")) == "Content-Disposition") {
				$contentfilename = explode ("\"", $wrapper_data);
				$this->_filename = $contentfilename[1];
			}
		}
		return strlen($header);
	}

	function downloadFile($url, $target=false, &$params=null)
	{
		if(!function_exists('curl_init'))
		{
			$error_object = new stdClass();
			$error_object->number = 40;
			$error_object->message = 'cURL support not available on this host. Use fopen instead.';
			return $error_object;
		}
		
		$config =& JFactory::getConfig();
		
		if(is_null($params)) {
			$params = new JParameter();
		}
		
		$php_errormsg = '';								// Set the error message
		$track_errors = ini_set('track_errors',true);	// Set track errors

		// create a new cURL resource
		$ch = curl_init();

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);						// Set the download URL
		curl_setopt($ch, CURLOPT_HEADER, false);					// Don't include the header in the output
		curl_setopt($ch, CURLOPT_USERAGENT, RokUpdater::generateUAString());	// set the user agent
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 			// follow redirects (required for Joomla!)
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 					// 10 maximum redirects
//		curl_setopt($ch, CURLOPT_HEADERFUNCTION, 					// set a custom header callback
//				array($this, 'header_callback'));					// use this object and function 'header_callback'

		if (!$target) {
			$target = $config->getValue('config.tmp_path').DS.Downloader::getFilenameFromURL($url);
		}
		
		RokUpdater::import('pasamio.pfactory');
		$output_stream = PFactory::getStream(true, true, 'RokUpdater/'.ROKUPDATER_VERSION, true);
		$relative_target = str_replace(JPATH_ROOT, '', $target);
		$output_handle = $output_stream->open($relative_target, 'wb');
		//$output_handle = @fopen($target, "wb"); 								// Open a location
		if (!$output_handle)
		{
			$error_object->number = 43;
			$error_object->message = 'Local output opening failed: ' . $output_stream->getError();
			ini_set('track_errors',$track_errors);
			return $error_object;
		}

		// since we're using streams we should be able to get everything up and running fine here
		curl_setopt($ch, CURLOPT_FILE, $output_stream->getFileHandle());
		if($params->get('use_proxy', 0) && strlen($params->get('proxy_host', '')) && strlen($params->get('proxy_port', '')))
		{
			curl_setopt($ch, CURLOPT_PROXY, $params->get('proxy_host') . ':' . $params->get('proxy_port'));
			if(strlen($params->get('proxy_user', '')))
			{
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $params->get('proxy_user') . ':' . $params->get('proxy_pass', ''));
			}
		}


		// grab URL and pass it to the browser
		if(curl_exec($ch) === false)
		{
			$error_object->number = 46;
			$error_object->message = 'cURL transfer failed('. curl_errno($ch) .'): ' . curl_error($ch);
			ini_set('track_errors',$track_errors);
			return $error_object;
		}

		// close cURL resource, and free up system resources
		curl_close($ch);
		$output_stream->close();
		return basename($target);
	}
}