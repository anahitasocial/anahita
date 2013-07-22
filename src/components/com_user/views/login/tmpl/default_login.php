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

<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang = &JFactory::getLanguage();
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var comlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>

<module position="sidebar-b" style="basic"></module>

<div class="rt-joomla <?php print $this->escape($this->params->get('pageclass_sfx')); ?>">
	
	<div class="user">

		<?php if ( $this->params->get( 'show_login_title' ) ) : ?>
		<h1>
			<?php print $this->params->get( 'header_login' ); ?>
		</h1>
		<?php endif; ?>

		<?php if ($this->params->get('description_login') || $this->image) : ?>
		<div class="rt-description">
			<?php if ( $this->params->get( 'description_login' ) ) : ?>
				<?php print $this->params->get( 'description_login_text' ); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<form action="<?php print JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>" method="post" name="com-login" id="com-form-login">
		<fieldset>
			<legend><?php print JText::_('LOGIN') ?></legend>
			
			<?php KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service')?>
			<?php if ( class_exists('ComConnectTemplateHelperService', true) ): ?>
			<div class="connect-service-actions">
			<?php echo KService::get('com://site/connect.template.helper.service')->renderLogins() ?>
			</div>
			<?php endif; ?>
			
			<div class="control-group">
				<label class="control-label"  for="username"><?php print JText::_('USERNAME OR EMAIL') ?></label>
				<div class="controls">
					<input name="username" id="username" type="text" alt="username" size="18" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="passwd"><?php print JText::_('Password') ?></label>
				<div class="controls">
					<input type="password" id="passwd" name="passwd" size="18" alt="password" />
				</div>
			</div>
			
			<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div id="form-login-remember" class="control-group">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="yes" alt="<?php print JText::_('Remember me'); ?>" />
					<?php print JText::_('Remember me'); ?>
				</label>
			</div>
			<?php endif; ?>
			<div class="form-actions">
				<input type="submit" name="Submit" class="btn btn-primary" value="<?php print JText::_('LOGIN') ?>" />
			</div>
			
		</fieldset>
	
		<ul>
			<li>
				<a href="<?php print JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
					<?php print JText::_('FORGOT_YOUR_PASSWORD'); ?>
				</a>
			</li>
			<li>
				<a href="<?php print JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
					<?php print JText::_('FORGOT_YOUR_USERNAME'); ?>
				</a>
			</li>
			<?php $usersConfig = &JComponentHelper::getParams( 'com_users' ); ?>
			<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<a href="<?php print JRoute::_( 'index.php?option=com_user&task=register' ); ?>"><?php print JText::_('REGISTER'); ?></a>
			</li>
			<?php endif; ?>
		</ul>

		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php print $this->return; ?>" />
		<?php print JHTML::_( 'form.token' ); ?>
		</form>

	</div>
</div>