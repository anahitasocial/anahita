<ul class="search-scopes nav nav-pills nav-stacked">
<?php if ( $items->getScopes()->getTotal() ) : ?>
	<?php $groups = @helper('scopes.group', $items->getScopes()) ?>
	<?php foreach($groups as $name => $scopes) : ?>	
	<?= @template('_scopes', array('scopes'=>$scopes,'header'=>$name))?>
	<?php endforeach;?>	
<?php endif;?>
</ul>
