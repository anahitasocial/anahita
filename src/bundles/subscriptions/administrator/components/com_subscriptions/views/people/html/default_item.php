<? defined('KOOWA') or die('Restricted access'); ?>

<? $i = 0; $m = 0; ?>
<? foreach ($people as $person) : ?>
<tr class="<?php echo 'row'.$m; ?>">
	<td align="center"><?= $i + 1; ?></td>
	<td>	
		<a href="<?=@route(array('view'=>'person','id'=>$person->id))?>">
			<?= $person->name ?>
		</a>
	</td>
	<td align="center"><?= $person->email ?></td>	
	<td align="center"><?= $person->username ?></td>	
	<td align="center"><?= $person->id; ?></td>
</tr>
<? $i = $i + 1; $m = (1 - $m); ?>
<? endforeach; ?>