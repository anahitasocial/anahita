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
defined('_JEXEC') or die('Restricted access'); ?>
<div class="login-module">
<?php if($type == 'logout') : ?>
<form action="index.php" method="post" name="logout" id="logout-form">
<?php if ($params->get('greeting')) : ?>
	
	<div class="login-avatar">
	<?php
		$viewer = get_viewer();
		print KService::get('com://site/actors.template.helper')->avatar($viewer);
	?>
	</div>

	<div class="login-greeting">
	<?php if ($params->get('name')) : {
		echo JText::sprintf( 'HINAME', $user->get('name') );
	} else : {
		echo JText::sprintf( 'HINAME', $user->get('username') );
	} endif; ?>
	
		<a href="#" onclick="$('logout-form').submit()"><?php echo JText::_('BUTTON_LOGOUT'); ?></a>
	</div>
<?php endif; ?>
	<div class="clearfix"></div>

	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
</form>
<?php else : ?>
<?php KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service')?>
<?php if (  class_exists('ComConnectTemplateHelperService', true) ): ?>
<div class="connect-service-actions">
<?php echo KService::get('com://site/connect.template.helper.service')->renderLogins() ?>
</div>
<?php endif; ?>

<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var modlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login">
	<?php echo $params->get('pretext'); ?>
	
	<div class="control-group">
		<label class="control-label" for="username"><?php print JText::_('USERNAME OR EMAIL') ?></label>
		<div class="controls">
			<input id="modlgn_username" type="text" name="username" alt="username" size="18" />
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="password"><?php print JText::_('Password') ?></label>
		<div class="controls">
			<input id="modlgn_passwd" type="password" name="passwd" size="18" alt="password" />
		</div>
	</div>
	
	<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div class="control-group">
		<label class="checkbox">
			<input type="checkbox" name="remember" value="yes" alt="<?php echo JText::_('Remember me'); ?>" />
			<?php print JText::_('Remember me'); ?>
		</label>
	</div>
	<?php endif; ?>
	
	<div class="form-actions">
		<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('LOGIN') ?>" />
	</div>

	<ul>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
			<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
			<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=register' ); ?>">
				<?php echo JText::_('REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<?php echo $params->get('posttext'); ?>

	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php endif; ?>
</div>