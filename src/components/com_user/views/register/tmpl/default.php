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
	
		<?php if ( $this->params->get( 'show_page_title', 1) ) : ?>
		<h1 class="rt-pagetitle">
			<?php print $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<?php if(isset($this->message)) : ?>
			<?php $this->display('message'); ?>
		<?php endif; ?>

		<form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php?option=com_user' ); ?>" method="post" name="josForm">
		
		<fieldset>
			<legend></legend>
			
			<div class=control-group">
				<label class="control-label"  for="name">
					<?php print JText::_( 'Name' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required" type="text" name="name" id="name" value="<?php print $this->escape($this->user->get( 'name' ));?>" maxlength="50" /> *		
				</div>
			</div>
			
			<div class=control-group">
				<label class="control-label"  for="username">
					<?php print JText::_( 'User name' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required validate-remote url:'index.php?option=com_people&view=person'" type="text" id="username" name="username" value="<?php print $this->escape($this->user->get( 'username' ));?>" maxlength="25" /> *
				</div>
			</div>
			
			<div class=control-group">
				<label class="control-label"  for="email">
					<?php print JText::_( 'Email' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required validate-email validate-remote url:'index.php?option=com_people&view=person'" type="text" id="email" name="email" value="<?php print $this->escape($this->user->get( 'email' ));?>" maxlength="100" /> *
				</div>
			</div>
			
			<div class=control-group">
				<label class="control-label"  for="password">
					<?php print JText::_( 'Password' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required" type="password" id="password" name="password" value="" /> *
				</div>
			</div>
			
			<div class=control-group">
				<label class="control-label"  for="password2">
					<?php print JText::_( 'Verify Password' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required validate-match matchInput:'password' matchName:'<?php print JText::_( 'Password' )?>'" type="password" id="password2" name="password2" value="" /> *
				</div>
			</div>
			
			<div class="alert alert-info">
				<?php print JText::_( 'REGISTER_REQUIRED' ); ?>
			</div>
			
			<div class="form-actions">
				<input type="submit" name="Submit" class="btn btn-primary validate" value="<?php print JText::_('Register'); ?>" />
			</div>
		</fieldset>
		
		<input type="hidden" name="task" value="register_save" />
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="gid" value="0" />
		<?php print JHTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>