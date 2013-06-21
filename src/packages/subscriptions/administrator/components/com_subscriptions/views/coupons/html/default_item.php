<? defined('KOOWA') or die('Restricted access'); ?>

<? $i = 0; $m = 0; ?>
<? foreach ($coupons as $coupon) : ?>
<tr class="<?php echo 'row'.$m; ?>">
	<td align="center"><?= $i + 1; ?></td>
	<td align="center"><?= @helper('grid.checkbox', array('row'=>$coupon)); ?></td>
	<td>
		<a href="<?= @route('view=coupon&id='.$coupon->id.'&hidemainmenu=1')?>">
			<?= $coupon->code ?>				
		</a>			
	</td>
	<td align="center"><?= $coupon->discount * 100?>%</td>	
	<td align="center"><?= $coupon->limit ?> </td>
	
	<td align="center"><?= $coupon->id; ?></td>
</tr>
<? $i = $i + 1; $m = (1 - $m); ?>
<? endforeach; ?>