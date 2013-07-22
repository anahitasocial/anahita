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
	
		<?php if ( $this->params->get( 'show_page_title', 1 ) ) : ?>
		<h1 class="rt-pagetitle">
			<?php print $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php endif; ?>

		<form data-behavior="FormValidator" action="<?php print JRoute::_( 'index.php' ); ?>" method="post" name="userform" id="userform" autocomplete="off">
		<fieldset>
			<legend></legend>

			<div class="control-group">
				<label class="control-label" for="username">
					<?php print JText::_( 'User Name' ); ?>:
				</label>
				<div class="controls">
					<input class="large disabled" type="text" placeholder="<?php print $this->user->get('username'); ?>"  disabled/>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="name">
					<?php print JText::_( 'Your Name' ); ?>:
				</label>
				<div class="controls">
				<input class="large disabled" type="text" id="name" name="name" value="<?php print $this->escape($this->user->get('name'));?>" disabled/> *
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="email">
					<?php print JText::_( 'email' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required validate-email validate-remote url:'index.php?option=com_people&view=person'"  class="large" type="text" id="email" name="email" value="<?php print $this->escape($this->user->get('email'));?>" /> *
				</div>
			</div>
			
			<?php if($this->user->get('password')) : ?>
			<div class="control-group">
				<label class="control-label" for="password">
					<?php print JText::_( 'Password' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required" class="large" type="password" id="password" name="password" value="" /> *
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="password2">
					<?php print JText::_( 'Verify Password' ); ?>:
				</label>
				<div class="controls">
					<input data-validators="required validate-match matchInput:'password' matchName:'<?php print JText::_( 'Password' )?>'" type="password" id="password2" name="password2" value="" /> *
				</div>
			</div>
			<?php endif; ?>
			
			<?php if(isset($this->params)): ?>
			<?php $params = $this->params->renderToArray() ?>
			<div class="control-group">
				<label class="control-label" for="timezone">
					<?php print JText::_( 'Time Zone' ); ?>:
				</label>
				<div class="controls">
					<?php print $params['timezone'][1] ?>
				</div>
			</div>
			<?php endif; ?>
			
			<div class="form-actions">
				<button type="submit" class="btn" onclick="submitbutton( this.form );return false;"><?php print JText::_('Save'); ?></button>
			</div>
			
		</fieldset>
		<input type="hidden" name="username" value="<?php print $this->user->get('username');?>" />
		<input type="hidden" name="id" value="<?php print $this->user->get('id');?>" />
		<input type="hidden" name="gid" value="<?php print $this->user->get('gid');?>" />
		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="save" />
		<?php print JHTML::_( 'form.token' ); ?>
		</form>

	</div>
</div>