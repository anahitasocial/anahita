<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Site Settings' ); ?></legend>
	<table class="admintable" cellspacing="1">
	<tbody>
		<tr>
			<td class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Site Name' ); ?>::<?php echo JText::_( 'TIPSITENAME' ); ?>">
				<?php echo JText::_( 'Site Name' ); ?>
				</span>
			</td>
			<td>
				<input class="text_area" type="text" name="sitename" size="100%" value="<?php echo $row->sitename; ?>" />
			</td>
		</tr>
	</tbody>
	</table>
</fieldset>
