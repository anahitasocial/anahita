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

<form action="<?php print JRoute::_( 'index.php?option=com_search#content' ) ?>" method="post" class="form-stacked">
<a name="form1"></a>

<fieldset>
<div class="clearfix">
	<label for="search_searchword"><?php print JText::_('Search Keyword') ?> </label>
	<div class="input">
		<input type="text" name="searchword" id="search_searchword"  maxlength="20" value="<?php print $this->escape($this->searchword) ?>" />
	</div>
</div>
</fieldset>

<fieldset>
	<legend><?php print JText::_('Search Parameters') ?></legend>
	
	<div class="clearfix">
	<label for="ordering"><?php print JText::_('Ordering') ?>:</label>
		
		<div class="input">
		<?php print $this->lists['ordering']; ?>
		</div>
		
		<div class="input">
		<?php print $this->lists['searchphrase']; ?>
		</div>
	</div>
</fieldset>

<?php if ($this->params->get('search_areas', 1)) : ?>
<fieldset class="only">
	<legend><?php print JText::_('Search Only') ?>:</legend>
	<?php foreach ($this->searchareas['search'] as $val => $txt) : ?>
		<?php $checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="true"' : ''; ?>
		<label for="area_<?php print $val ?>">
		<input type="checkbox" name="areas[]" value="<?php print $val ?>" id="area_<?php print $val ?>" <?php print $checked ?> />
		<?php print JText::_($txt); ?>
		</label>
	<?php endforeach; ?>
</fieldset>
<?php endif; ?>

<div class="clearfix">
	<input type="submit" name="Search" onClick="this.form.submit()" class="btn primary" value="<?php print JText::_( 'Search' );?>" />
</div>


<?php if (count($this->results)) : ?>
<div class="display">
<label for="limit"><?php print JText :: _('Display Num') ?></label>
	<?php print $this->pagination->getLimitBox(); ?>
	<p>
		<?php print $this->pagination->getPagesCounter(); ?>
	</p>
</div>
<?php endif; ?>

<input type="hidden" name="task"   value="search" />
</form>
