<?php

class PFactory extends JFactory {
	/**
	 * Creates a new stream object with appropriate prefix
	 * @param boolean Prefix the connections for writing
	 * @param boolean Use network if available for writing; use false to disable (e.g. FTP, SCP)
	 * @param string UA User agent to use
	 * @param boolean User agent masking (prefix Mozilla)
	 */
	function &getStream($use_prefix=true, $use_network=true,$ua=null, $uamask=false) {
		RokUpdater::import('joomla.filesystem.stream');
		RokUpdater::import('pasamio.pversion');
		jimport('joomla.client.helper');
		// Setup the context; Joomla! UA and overwrite
		$context = Array();
		$version = new PVersion();
		// set the UA for HTTP and overwrite for FTP
		$context['http']['user_agent'] = $version->getUserAgent($ua, $uamask);
		$context['ftp']['overwrite'] = true;
		if($use_prefix) {
			$FTPOptions = JClientHelper::getCredentials('ftp');
			$SCPOptions = JClientHelper::getCredentials('scp');
			if ($FTPOptions['enabled'] == 1 && $use_network) {
				$prefix = 'ftp://'. $FTPOptions['user'] .':'. $FTPOptions['pass'] .'@'. $FTPOptions['host'];
				$prefix .= $FTPOptions['port'] ? ':'. $FTPOptions['port'] : '';
				$prefix .= $FTPOptions['root'];
			} else if($SCPOptions['enabled'] == 1 && $use_network) {
				$prefix = 'ssh2.sftp://'. $SCPOptions['user'] .':'. $SCPOptions['pass'] .'@'. $SCPOptions['host'];
				$prefix .= $SCPOptions['port'] ? ':'. $SCPOptions['port'] : '';
				$prefix .= $SCPOptions['root'];
			} else {
				$prefix = JPATH_ROOT.DS;
			}
			$retval = new JStream($prefix, JPATH_ROOT, $context);
		} else {
			$retval = new JStream('','',$context);
		}
		return $retval;
	}
}