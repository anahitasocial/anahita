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

		<h1 class="rt-pagetitle">
			<?php print JText::_('Confirm your Account'); ?>
		</h1>

		<p><?php print JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></p>

		<form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php?option=com_user&task=confirmreset' ); ?>" method="post">
		<fieldset>
			<legend><?php print JText::_('Confirm your Account'); ?></legend>
			
			<div class="control-group">
				<label class="control-label" for="username"><?php print JText::_('User Name'); ?>:</label>
				<div class="controls">
					<input id="username" name="username" type="text" data-validators="required" size="36" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="token"><?php print JText::_('Token'); ?>:</label>
				<div class="controls">
					<input id="token" name="token" type="text" size="36" />
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