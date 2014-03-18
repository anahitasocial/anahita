<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Server Settings' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
			<tr>
				<td valign="top" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Path to Temp-folder' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
						<?php echo JText::_( 'Path to Temp-folder' ); ?>
					</span>
				</td>
				<td>
					<input class="text_area" type="text" size="100%" name="tmp_path" value="<?php echo $row->tmp_path; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Error Reporting' ); ?>::<?php echo JText::_( 'TIPERRORREPORTING' ); ?>">
						<?php echo JText::_( 'Error Reporting' ); ?>
					</span>
				</td>
				<td>
					<?php echo $lists['error_reporting']; ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
