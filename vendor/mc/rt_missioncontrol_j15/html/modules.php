<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_sidebar($module, &$params, &$attribs)
{
	if($module->content)
	{
		?>
		<div class="mc-module-sidebar <?php echo $params->get('extra_class'); ?>">
			<h2><?php echo $module->title; ?></h2>
			<div class="mc-module-content">
				<?php echo $module->content; ?>
			</div>
		</div>
		<?php
	}
}

function modChrome_standard($module, &$params, &$attribs)
{
	if($module->content)
	{
		?>
		<div class="mc-module-standard <?php echo $params->get('extra_class'); ?>">
			<h2><?php echo $module->title; ?></h2>
			<div class="mc-module-content">
				<?php echo $module->content; ?>
			</div>
		</div>
		<?php
	}
}
?>