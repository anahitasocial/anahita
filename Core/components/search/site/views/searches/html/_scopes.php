<?php defined('KOOWA') or die; ?>

<?php if ( count($scopes) ) : ?>
<li class="nav-header">
	<?= @text('COM-SEARCH-SCOPE-HEADER-'.$header) ?>
</li>	
	
<?php foreach($scopes as $scope ) : ?>
<li class="<?= ($current_scope == $scope) ? 'active' : ''?>" >
	<a data-trigger="ChangeScope" href="<?= @route('layout=results&scope='.$scope->getKey()) ?>">
		<?= @text(strtoupper($scope->identifier->type.'-'.$scope->identifier->package.'-SEARCH-SCOPE-'.$scope->identifier->name)) ?>
	</a>
</li>
<?php endforeach;?>
<?php endif ?>