<?php defined('KOOWA') or die('Restricted access'); ?>

<form action="index.php" method="get" class="adminForm">
	<input type="hidden" value="people" name="view" />
	<input type="hidden" value="com_subscriptions" name="option" />
	<table width="100%" class="mc-filter-table mc-first-table">
		<tr>
			<td class="mc-first-cell" width="100%">
				<?= @text( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?= empty($search) ? '' : $search ?>" class="text_area"  />
				<?= @html('button','Go')->onclick("this.form.submit()")   ?>
				<?= @html('button','Reset')->onclick("this.form.search.value='';this.form.submit();return false;")?>
			</td>		
				
			<td class="mc-last-cell">
				<select name="package" onchange="this.form.submit()">
					<?= @helper('packages', $package)?>
				</select>
			</td>
		</tr>
	</table>
</form>

<form action="<?= @route()?>" method="post" class="-koowa-grid">
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="action" value="" />
	<table class="adminlist mc-list-table mc-second-table" cellspacing="1">
		<thead>
			<tr>				
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="20%"><?= @helper('grid.sort', array('column'=>'person','sort'=>$sort)); ?></th>
				<th width="10%"><?= @helper('grid.sort', array('column'=>'email','sort'=>$sort)); ?></th>
				<th width="10%"><?= @helper('grid.sort', array('column'=>'username','sort'=>$sort)); ?></th>
				<th width="1%"><?= @helper('grid.sort', array('column'=>'id','sort'=>$sort)); ?></th>
			</tr>
		</thead>
		
		<tbody>		
		<?= @template('default_item') ?>
		<?php if (!$people->getTotal()) : ?>
			<tr>
				<td colspan="4" align="center">
					<?= @text('AN-SB-NO-PEOPLE'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
		
		<tfoot>
			<tr>
				<td colspan="5">
					<?= @helper('paginator.pagination', array('total'=>$people->getTotal(), 'offset'=>$people->getOffset(), 'limit'=>$people->getLimit())) ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>