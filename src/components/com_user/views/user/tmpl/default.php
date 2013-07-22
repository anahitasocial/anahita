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
		<div class="alert alert-success">
			<?php print nl2br($this->escape($this->params->get('welcome_desc', JText::_( 'WELCOME_DESC' )))); ?>
		</div>
	</div>
</div>