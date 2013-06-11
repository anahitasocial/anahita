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

<script language="javascript" type="text/javascript">
<!--
function tableOrdering( order, dir, task ) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}
// -->
</script>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm">

<?php if ($this->params->get('filter') || $this->params->get('show_pagination_limit')) : ?>
<div class="filter">
	<?php if ($this->params->get('filter')) : ?>
		<?php echo JText::_($this->params->get('filter_type') . ' Filter'); ?>
		&nbsp;<input type="text" name="filter" value="<?php echo $this->escape($this->lists['filter']);?>" onchange="document.adminForm.submit();" />
	<?php endif; ?>
	
	<?php if ($this->params->get('show_pagination_limit')) : ?>
		&nbsp;&nbsp;&nbsp;<?php	echo JText::_('Display Num'); ?>
		&nbsp;<?php echo $this->pagination->getLimitBox(); ?>
	<?php endif; ?>
</div>
<?php endif; ?>

<table class="table table-striped">

	<?php if ($this->params->get('show_headings')) : ?>
	<thead>
		<tr>
			<th>
				<?php echo JText::_('Num'); ?>
			</th>
			<?php if ($this->params->get('show_title')) : ?>
			<th>
				<?php echo JHTML::_('grid.sort',  'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_date')) : ?>
			<th>
				<?php echo JHTML::_('grid.sort',  'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_author')) : ?>
			<th>
				<?php echo JHTML::_('grid.sort',  'Author', 'author', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_hits')) : ?>
			<th>
				<?php echo JHTML::_('grid.sort',  'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<?php endif; ?>
		</tr>
	</thead>
	<?php endif; ?>
	
	<tbody>
	<?php foreach ($this->items as $item) : ?>
	<tr>
		<td>
			<?php echo $this->pagination->getRowOffset( $item->count ); ?>
		</td>
		<?php if ($this->params->get('show_title')) : ?>
			<?php if ($item->access <= $this->user->get('aid', 0)) : ?>
			<td>
				<a href="<?php echo $item->link; ?>"><?php echo $this->escape($item->title); ?></a>
			</td>
			<?php else : ?>
			<td>
				<?php
					echo $this->escape($item->title).' : ';
					$link = JRoute::_('index.php?option=com_user&view=login');
					$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid), false);
					$fullURL = new JURI($link);
					$fullURL->setVar('return', base64_encode($returnURL));
					$link = $fullURL->toString();
				?>
				<a href="<?php echo $link; ?>"><?php echo JText::_( 'Register to read more...' ); ?></a>
			</td>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_date')) : ?>
		<td>
			<?php echo $item->created; ?>
		</td>
		<?php endif; ?>
		<?php if ($this->params->get('show_author')) : ?>
		<td >
			<?php echo $this->escape($item->created_by_alias) ? $this->escape($item->created_by_alias) : $this->escape($item->author); ?>
		</td>
		<?php endif; ?>
		<?php if ($this->params->get('show_hits')) : ?>
		<td>
			<?php echo $this->escape($item->hits) ? $this->escape($item->hits) : '-'; ?>
		</td>
		<?php endif; ?>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if ($this->params->get('show_pagination')) : ?>
<div class="rt-pagination">
	<p class="rt-results">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="sectionid" value="<?php echo $this->category->sectionid; ?>" />
<input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>" />
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="limitstart" value="0" />
</form>
