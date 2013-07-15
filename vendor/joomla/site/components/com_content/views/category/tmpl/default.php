<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   3.1.4 November 12, 2010
 * @author    YOOtheme http://www.yootheme.com & RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2009 YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * These template overrides are based on the fantastic GNU/GPLv2 overrides created by YOOtheme (http://www.yootheme.com)
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="rt-joomla <?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<div class="rt-category-list">
		<?php /** Begin Page Title **/ if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="rt-pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php /** End Page Title **/ endif; ?>

		<?php /** Begin Category Description **/ if ($this->category->image || $this->category->description) : ?>
		<div class="rt-description">			
			<?php if ($this->category->description) : ?>
				<?php echo $this->category->description; ?>
			<?php endif; ?>
		</div>
		<?php /** End Category Description **/ endif; ?>

		<?php
			$this->items =& $this->getItems();
			echo $this->loadTemplate('items');
		?>
	</div>
</div>