<?php
/**
 * @version		$Id: sysinfo_config.php 12694 2009-09-11 21:03:02Z ian $
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Configuration File' ); ?></legend>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="300">
					<?php echo JText::_( 'Setting' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Value' ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
			<?php
			$cf = file( JPATH_CONFIGURATION . '/configuration.php' );
			$config_output = array();
			foreach ($cf as $k => $v) {
				if (preg_match( '#var \$host#i', $v)) {
					$cf[$k] = 'var $host = \'xxxxxx\'';
				} else if (preg_match( '#var \$user#i', $v)) {
					$cf[$k] = 'var $user = \'xxxxxx\'';
				} else if (preg_match( '#var \$password#i', $v)) {
					$cf[$k] = 'var $password = \'xxxxxx\'';
				} else if (preg_match( '#var \$db#i', $v)) {
					$cf[$k] = 'var $db = \'xxxxxx\'';
				} else if (preg_match( '#var \$ftp_user#i', $v)) {
					$cf[$k] = 'var $ftp_user = \'xxxxxx\'';
				} else if (preg_match( '#var \$ftp_pass#i', $v)) {
					$cf[$k] = 'var $ftp_pass = \'xxxxxx\'';
				} else if (preg_match( '#var \$smtpuser#i', $v)) {
					$cf[$k] = 'var $smtpuser = \'xxxxxx\'';
				} else if (preg_match( '#var \$smtppass#i', $v)) {
					$cf[$k] = 'var $smtppass = \'xxxxxx\'';
				} else if (preg_match( '#<\?php#i', $v)) {
					$cf[$k] = '';
				} else if (preg_match( '#\?>#i', $v)) {
					$cf[$k] = '';
				} else if (preg_match( '#\}#i', $v)) {
					$cf[$k] = '';
				} else if (preg_match( '#class JConfig \{#i', $v)) {
					$cf[$k] = '';
				}
				$cf[$k]		= str_replace('var ','',$cf[$k]);
				$cf[$k]		= str_replace(';','',$cf[$k]);
				$cf[$k]		= str_replace(' = ','</td><td>',$cf[$k]);
				$cf[$k]		= '<td>'. $cf[$k] .'</td>';
				if ($cf[$k] != '<td></td>') {
					$config_output[] = $cf[$k];
				}
			}
			echo implode( '</tr><tr>', $config_output );
			?>
			</tr>
		</tbody>
		</table>
</fieldset>
