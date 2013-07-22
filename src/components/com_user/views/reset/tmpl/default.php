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

<div class="rt-joomla <?php print $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="user">
		<?php if ($this->params->get( 'show_page_title', 1) ) : ?>
		<h1 class="rt-pagetitle">
			<?php print $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<p><?php print JText::_('RESET_PASSWORD_REQUEST_DESCRIPTION'); ?></p>

		<form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php?option=com_user&task=requestreset' ); ?>" method="post">
		<fieldset>
			<legend><?php print JText::_( 'RESET YOUR PASSWORD' ) ?></legend>

			<div class="control-group">
				<label class="control-label" for="email"><?php echo JText::_('Email Address'); ?>:</label>
				<div class="controls">
					<input id="email" name="email" type="text" data-validators="required validate-email" />
				</div>
			</div>
		
			<div class="form-actions">
				<button type="submit" class="btn btn-primary"><?php print JText::_('Submit'); ?></button>
			</div>
		</fieldset>
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>