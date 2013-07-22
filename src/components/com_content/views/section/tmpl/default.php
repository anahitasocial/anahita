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
	<div class="rt-section-list">
		<?php /** Begin Page Title **/ if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="rt-pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php /** End Page Title **/ endif; ?>
		
		<?php /** Begin Description **/ if (($this->params->get('show_description_image') && $this->section->image) || ($this->params->get('show_description') && $this->section->description)) : ?>
		<div class="rt-description">			
			<?php if ($this->params->get('show_description') && $this->section->description) : ?>
				<?php echo $this->section->description; ?>
			<?php endif; ?>
		</div>
		<?php /** End Description **/ endif; ?>

		<?php /** Begin Categories **/ if ($this->params->get('show_categories', 1)) : ?>
		<ul>
			<?php foreach ($this->categories as $category) : ?>
			<?php if (!$this->params->get('show_empty_categories') && !$category->numitems) continue; ?>
			<li>
				<a href="<?php echo $category->link; ?>" class="category"><?php echo $this->escape($category->title);?></a>
				<?php if ($this->params->get('show_cat_num_articles')) : ?>
				&nbsp;
				<span class="number">
					( <?php if ($category->numitems==1) {
					echo $category->numitems ." ". JText::_( 'item' );}
					else {
					echo $category->numitems ." ". JText::_( 'items' );} ?> )
				</span>
				<?php endif; ?>
				<?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
				<br />
				<?php echo $category->description; ?>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php /** End Categories **/ endif; ?>
	</div>
</div>