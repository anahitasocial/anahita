<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   3.1.4 November 12, 2010
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<module position="sidebar-b" style="basic"></module>

<div class="rt-joomla <?php print $this->params->get('pageclass_sfx')?>">
	
	<div class="user">

		<h1 class="rt-pagetitle"><?php print JText::_('Reset your Password'); ?></h1>
		
		<p><?php print JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>
	
		<form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post">
		<fieldset>
			<legend><?php print JText::_('Reset your Password'); ?></legend>
			
			<div class="control-group">
				<label class="control-label" for="password1"><?php print JText::_('Password'); ?>:</label>
				<div class="controls">
					<input data-validators="required" id="password1" name="password1" type="password" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="password2"><?php print JText::_('Verify Password'); ?>:</label>
				<div class="controls">
					<input data-validators="required validate-match matchInput:'password1' matchName:'<?php print JText::_( 'Password' )?>'" type="password" id="password2" name="password2" value="" />
				</div>
			</div>
			
			<div class="form-actions">
				<button type="submit" class="btn btn-primary"><?php print JText::_('Submit'); ?></button>
			</div>
			
		</fieldset>
		<?php print JHTML::_( 'form.token' ); ?>
		</form>
		
	</div>
</div>