<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Route Setting' ); ?></legend>
	<table class="admintable" cellspacing="1">

		<tbody>
		<tr>
			<td width="185" class="key">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Use mod_rewrite' ); ?>::<?php echo JText::_('TIPUSEMODREWRITE'); ?>">
					<?php echo JText::_( 'Use mod_rewrite' ); ?>
				</span>
			</td>
			<td>
				<?php echo $lists['sef_rewrite']; ?>				
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
