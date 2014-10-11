<?php defined('KOOWA') or die; ?>

<form data-trigger="SearchRequest" action="<?= @route($url) ?>" class="navbar-search pull-left">
	<input type="text" name="term" value="<?= $term ?>" class="search-query"  placeholder="<?= $label ?>">
	
	<?php if($actor) : ?>
	<input type="hidden" name="oid" value="<?= $actor->id ?>" />
	<?php endif;?>
	
	<?php if($scope) : ?>
	<input type="hidden" name="scope" value="<?= $scope->getKey() ?>" />
	<?php endif;?>
</form>