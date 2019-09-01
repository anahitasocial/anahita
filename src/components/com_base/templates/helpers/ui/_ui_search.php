<? defined('ANAHITA') or die; ?>

<form id="navbar-search" data-trigger="SearchRequest" action="<?= @route($url) ?>" class="navbar-search pull-left">
	<input 
		type="text" 
		name="term" 
		value="<?= $term ?>" 
		class="search-query" 
		placeholder="<?= $label ?>"
		maxlength="100"
	>
</form>
