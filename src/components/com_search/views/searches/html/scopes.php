<? defined('KOOWA') or die; ?>

<ul class="search-scopes nav nav-tabs nav-stacked">
	<li class="<?= empty($current_scope) ? 'active' : ''; ?>" >
		<a data-trigger="ChangeScope" data-scope="all" href="<?= @route('layout=list&scope=all') ?>">
			<?= @text('COM-SEARCH-SCOPE-HEADER-ALL') ?>
		</a>
	</li>

	<? $global = !isset($actor) ?>
	<? $groups = @helper('scopes.group', $items->getScopes(), $global) ?>
	<? foreach ($groups as $name => $scopes): ?>
	<? if (count($scopes)): ?>
	<li class="nav-header">
		<?= @text('COM-SEARCH-SCOPE-HEADER-'.$name) ?>
	</li>
	<? foreach ($scopes as $scope) : ?>
	<li class="<?= ($current_scope == $scope) ? 'active' : ''?>" >
		<a data-trigger="ChangeScope" data-scope="<?= $scope->getKey() ?>" href="<?= @route('layout=list&scope='.$scope->getKey()) ?>">
			<?= @text(strtoupper($scope->identifier->type.'-'.$scope->identifier->package.'-SEARCH-SCOPE-'.$scope->identifier->name)) ?>
		</a>
	</li>
	<? endforeach;?>
	<? endif ?>
	<? endforeach;?>
</ul>
