<?php

RokUpdater::import('joomla.base.adapterinstance');

class DownloaderFOpen extends JAdapterInstance {


	function downloadFile($url, $target = false, &$params=null)
	{
		// this isn't intelligent some times
		$error_object = new stdClass();
		$proxy = false;
		$php_errormsg = '';								// Set the error message
		$track_errors = ini_set('track_errors',true);	// Set track errors

		$config =& JFactory::getConfig();

		if(is_null($params)) {
			$params = new JParameter();
		}

		$input_handle = null;
		// Are we on a version of PHP that supports streams?
		if(version_compare(PHP_VERSION, '5.0.0', '>'))
		{
			// set the ua; we could use ini_set but it might not work
			$http_opts = Array('user_agent'=>RokUpdater::generateUAString());
			// If:
			// - the proxy is enabled,
			// - the host is set and the port are set
			// Set the proxy settings and create a stream context
			if($params->get('use_proxy', 0) && strlen($params->get('proxy_host', '')) && strlen($params->get('proxy_port', '')))
			{
				$proxy = true;
				// I hate eclipse sometimes
				// If the user has a proxy username set fill this in as well
				$http_opts['proxy'] = 'tcp://'. $params->get('proxy_host') . ':'. $params->get('proxy_port');
				$http_opts['request_fulluri'] = 'true'; // play nicely with squid
				if(strlen($params->get('proxy_user', '')))
				{
					$credentials = base64_encode($params->get('proxy_user', '').':'.$params->get('proxy_pass',''));
					$http_opts['header'] = "Proxy-Authorization: Basic $credentials\r\n";
				}
			}

			$context = stream_context_create(array('http'=>$http_opts));
			$input_handle = @fopen($url, 'r', false, $context);
		}
		else
		{
			// Open remote server
			ini_set('user_agent', generateUAString()); // set the ua
			$input_handle = @fopen($url, "r"); // or die("Remote server connection failed");
		}

		if (!$input_handle)
		{
			$error_object->number = 42;
			$error_object->message = 'Remote Server connection failed: ' . $php_errormsg .'; Using Proxy: '. ($proxy ? 'Yes' : 'No');
			ini_set('track_errors',$track_errors);
			return $error_object;
		}


		$meta_data = stream_get_meta_data($input_handle);
		foreach ($meta_data['wrapper_data'] as $wrapper_data)
		{
			if (substr($wrapper_data, 0, strlen("Content-Disposition")) == "Content-Disposition") {
				$contentfilename = explode ("\"", $wrapper_data);
				$target = $contentfilename[1];
			}
		}

		// Set the target path if not given
		if (!$target) {
			$target = $config->getValue('config.tmp_path').DS.Downloader::getFilenameFromURL($url);
		} else {
			$target = $config->getValue('config.tmp_path').DS.basename($target);
		}


		RokUpdater::import('pasamio.pfactory');
		$stream = PFactory::getStream(true, true, 'RokUpdater/'.ROKUPDATER_VERSION, true);
		$relative_target = str_replace(JPATH_ROOT, '', $target);
		$output_handle = $stream->open($relative_target, 'wb');
		//$output_handle = fopen($target, "wb"); // or die("Local output opening failed");
		if (!$output_handle)
		{
			$error_object->number = 43;
			$error_object->message = 'Local output opening failed: ' . $stream->getError();
			ini_set('track_errors',$track_errors);
			return $error_object;
		}

		$contents = '';
		$downloaded = 0;

		while (!feof($input_handle))
		{
			$contents = fread($input_handle, 4096);
			if($contents === false)
			{
				$error_object->number = 44;
				$error_object->message = 'Failed reading network resource at '.$downloaded.' bytes: ' . $php_errormsg;
				ini_set('track_errors',$track_errors);
				return $error_object;
			} else if(strlen($contents))
			{
				$write_res = $stream->write($contents);
				if($write_res == false)
				{
					$error_object->number = 45;
					$error_object->message = 'Cannot write to local target: ' . $stream->getError();
					ini_set('track_errors',$track_errors);
					return $error_object;
				}
				$downloaded += 1024;
			}
		}

		$stream->close();
		fclose($input_handle);
		ini_set('track_errors',$track_errors);
		return basename($target);
	}


}