<ul class="search-scopes nav nav-tabs nav-stacked">
	<li class="<?= empty($current_scope) ? 'active' : ''; ?>" >
		<a data-trigger="ChangeScope" data-scope="all" href="<?= @route('layout=list&scope=all') ?>">
			<?= @text('COM-SEARCH-SCOPE-HEADER-ALL') ?> 
			<span class="badge badge-info pull-right"><?= $items->getScopes()->getTotal() ?></span>
		</a>
	</li>

	<?php if($items->getScopes()->getTotal()) : ?>
	<?php $groups = @helper('scopes.group', $items->getScopes()) ?>
	<?php foreach($groups as $name => $scopes): ?>	
	<?php if(count($scopes)): ?>
	<li class="nav-header">
		<?= @text('COM-SEARCH-SCOPE-HEADER-'.$name) ?>
	</li>	
	<?php foreach($scopes as $scope ) : ?>
	<li class="<?= ($current_scope == $scope) ? 'active' : ''?>" >
		<a data-trigger="ChangeScope" data-scope="<?= $scope->getKey() ?>" href="<?= @route('layout=list&scope='.$scope->getKey()) ?>">
			<?= @text(strtoupper($scope->identifier->type.'-'.$scope->identifier->package.'-SEARCH-SCOPE-'.$scope->identifier->name)) ?> 
			<span class="badge badge-info pull-right"><?= $scope->result_count ?></span>
		</a>
	</li>
	<?php endforeach;?>
	<?php endif ?>
	<?php endforeach;?>	
	<?php endif;?>
</ul>
