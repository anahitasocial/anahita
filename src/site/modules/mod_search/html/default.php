<?php 
$actor = null;
$label = @text('TMPL-SEARCH-PLACEHOLDER');
if ( @service()->has('mod://site/search.owner') ) {
	$actor = @service('mod://site/search.owner');
	$label = sprintf(@text('TMPL-SEARCH-PLACEHOLDER-OWNER'), $actor->name);
}
$scope = null;
if ( @service()->has('mod://site/search.scope') ) {
	$scope = @service('mod://site/search.scope');	
}
$url = 'option=com_search';
if ( $actor ) {
	$url .= '&oid='.$actor->uniqueAlias;
}

$term = KRequest::get('get.term','raw');
$term = @service('com://site/search.filter.term')->sanitize($term);

?>
<form data-trigger="SearchRequest" action="<?=@route($url)?>" class="navbar-search pull-left">
	<input type="text" name="term" value="<?= $term ?>" class="search-query"  placeholder="<?= $label ?>">
	<?php if ($actor && false) : ?>
	<input type="hidden" name="oid" value="<?=$actor->id?>" />
	<?php endif;?>
	<?php if ($scope) : ?>
	<input type="hidden" name="scope" value="<?=$scope->getKey()?>" />
	<?php if ( $scope->commentable ) : ?>
	<?php endif;?>
	<input type="hidden" name="search_comments" value="1" />
	<?php endif;?>
</form>