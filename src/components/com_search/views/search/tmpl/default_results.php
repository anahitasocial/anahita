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
defined('_JEXEC') or die('Restricted access');

?>

<?php if (!empty($this->searchword)) : ?>
<div class="searchintro<?php print $this->escape($this->params->get('pageclass_sfx')) ?>">
	<p>
		<?php print JText::_('Search Keyword') ?> <strong><?php print $this->escape($this->searchword) ?></strong>
		<?php print $this->result ?>
	</p>
	<p>
		<a href="#form1" class="readon" onclick="document.getElementById('search_searchword').focus();return false" onkeypress="document.getElementById('search_searchword').focus();return false"><span><?php print JText::_('Search_again') ?></span></a>
	</p>
</div>
<?php endif; ?>

<?php if (count($this->results)) : ?>
	<h3><?php print JText :: _('Search_result'); ?></h3>
<div class="results">
	<?php $start = $this->pagination->limitstart + 1; ?>
	<ol class="list" start="<?php print  $start ?>">
		<?php foreach ($this->results as $result) : ?>
		<?php
		$text = $result->text;
		$text = preg_replace( '/\[.+?\]/', '', $text);
		?>	
		<li>
			<?php if ($result->href) : ?>
			<h4>
				<a href="<?php print JRoute :: _($result->href) ?>" <?php print ($result->browsernav == 1) ? 'target="_blank"' : ''; ?> >
					<?php print $this->escape($result->title); ?></a>
			</h4>
			<?php endif; ?>
			<?php if ($result->section) : ?>
			<p><?php print JText::_('Category') ?>:
				<span class="small">
					<?php print $this->escape($result->section); ?>
				</span>
			</p>
			<?php endif; ?>

			<div class="description">
			<?php print $result->text; ?>
			</div>
			<span class="small">
				<?php print $this->escape($result->created); ?>
			</span>
		</li>
		<?php endforeach; ?>
	</ol>
	<div class="rt-pagination">
	<?php print $this->pagination->getPagesLinks(); ?>
</div>

</div>
<?php else: ?>
<div class="noresults"></div>
<?php endif; ?>
