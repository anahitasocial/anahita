<?php foreach($items as $item) : ?>	
	<?php if ( !empty($item->subitems) ) : ?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<?= $item->name?>
				<b class="caret"></b>				
			</a>
			<ul class="dropdown-menu">			
			<?php foreach($item->subitems as $subitem) : ?>			
				<?= @template('type_'.$subitem->type, array('item'=>$subitem)) ?>
			<?php endforeach;?>
			</ul>			
		</li>
	<?php else : ?>		
		<?= @template('type_'.$item->type, array('item'=>$item)) ?>
	<?php endif;?>
<?php endforeach?>