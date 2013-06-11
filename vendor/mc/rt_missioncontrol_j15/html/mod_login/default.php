<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.language.helper');
//$browserLang = JLanguageHelper::detectLanguage();
// forced to default
$browserLang = null;
$lang =& JFactory::getLanguage();

//manually get the params
$module = JModuleHelper::getModule( 'mod_login', 'module-title' );
$params = new JParameter( $module->params );


?>
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
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" style="clear: both;">
	<div id="login-wrapper">
		<input name="username" id="modlgn_username" type="text" placeholder="Username" class="inputbox" size="15" />
		<input name="passwd" id="modlgn_passwd" type="password" placeholder="Password" class="inputbox" size="15" />
		
		<span class="button" onclick="login.submit();">
			<?php echo JText::_( 'Login' ); ?>
		</span>
		
	</div>

	<div class="clr"></div>
	<input type="submit" style="border: 0; padding: 0; margin: 0; width: 0px; height: 0px;" value="<?php echo JText::_( 'Login' ); ?>" />
	<input type="hidden" name="option" value="com_login" />
	<input type="hidden" name="task" value="login" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
