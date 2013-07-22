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
	<div class="rt-blog">

		<?php /** Begin Page Title **/ if ($this->params->get('show_page_title', 1)) : ?>
		<h1 class="rt-pagetitle">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>
		<?php /** End Page Title **/ endif; ?>
		
		<?php /** Begin Description **/ if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
		<div class="rt-description">
			<?php if ($this->params->get('show_description') && $this->section->description) : ?>
				<?php echo $this->section->description; ?>
			<?php endif; ?>
		</div>
		<?php /** End Description **/ endif; ?>
		
		<?php /** Begin Leading Articles **/ if ($this->params->def('num_leading_articles', 1)) : ?>
		<div class="rt-leading-articles">
			<?php for ($i = $this->pagination->limitstart; $i < ($this->pagination->limitstart + $this->params->get('num_leading_articles')); $i++) : ?>
			<?php if ($i >= $this->total) : break; endif; ?>
				<?php
					$this->item =& $this->getItem($i, $this->params);
					echo $this->loadTemplate('item');
				?>
		<?php endfor; ?>
		</div>
		<?php /** End Leading Articles **/ else : $i = $this->pagination->limitstart; endif; ?>

		<?php /** Begin Article Columns **/ 
		if ($i < $this->total) {

			// init vars
			if ($this->params->get('num_columns')<1) $this->params->set('num_columns',1);
			
			$count   = min($this->params->get('num_intro_articles', 4), ($this->total - $i));
			$rows    = ceil($count / $this->params->get('num_columns', 2));
			$columns = array();

			// create intro columns
			for ($j = 0; $j < $count; $j++, $i++) { 

				if ($this->params->get('multi_column_order', 1) == 0) {
					// order down
					$column = intval($j / $rows);
				} else {
					// order across
					$column = $j % $this->params->get('num_columns', 2);
				}

				if (!isset($columns[$column])) {
					$columns[$column] = '';
				}

				$this->item =& $this->getItem($i, $this->params);
				$columns[$column] .= $this->loadTemplate('item');
			}

			// render intro columns
			$count = count($columns);
			if ($count) {
				if ($count != 1) {
					echo '<div class="rt-teaser-articles multicolumns">';
				} else {
					echo '<div class="rt-teaser-articles">';
				}
				for ($j = 0; $j < $count; $j++) {
					$firstlast = "";
					if ($count != 1) {
						if ($j == 0) $firstlast = "first";
						if ($j == $count - 1) $firstlast = "last";
					}
					echo '<div class="'.$firstlast.' float-left width'.intval(100 / $count).'">'.$columns[$j].'</div>';
				}
				echo '</div>';
			}
		}
		/** End Article Columns **/ ?>

		<?php /** Begin Article Links **/ if ($this->params->def('num_links', 4) && ($i < $this->total)) : ?>
		<div class="rt-article-links">
			<?php
				$this->links = array_splice($this->items, $i - $this->pagination->limitstart);
				echo $this->loadTemplate('links');
			?>
		</div>
		<?php /** End Article Links **/ endif; ?>

		<?php /** Begin Pagination **/ if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
		<div class="rt-pagination">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="rt-results">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php /** End Pagination **/ endif; ?>
		
	</div>
</div>