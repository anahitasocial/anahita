<?php defined('KOOWA') or die; ?>

<?php if(!empty($keywords)) : ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_search/js/search.js" />
<?php else: ?>
<script src="com_search/js/min/search.min.js" />
<?php endif; ?>

<?php endif;?>

<div class="row">
    <div class="span4">
    <?php if(!empty($keywords)): ?>
    <?= @template('scopes') ?>
    <?php endif;?>
    </div>
    
	<div class="span8">	
	
		<?= @helper('ui.header', array()) ?>
	
		<form action="<?= @route('view=searches') ?>">
			<select data-trigger="SortOption" id="SortOptions" name="sort">
				<option <?= ($sort == 'relevant') ? 'selected' : '' ?> value="relevant">
					<?= @text('COM-SEARCH-OPTION-SORT-MOST-RELEVANT') ?>
				</option>
				<option <?= ($sort == 'recent') ? 'selected' : '' ?> value="recent">
					<?= @text('COM-SEARCH-OPTION-SORT-MOST-RECENT') ?>
				</option>
			</select>
			
			<label class="checkbox">
				<input data-trigger="SearchOption" <?= $search_comments ? 'checked' : ''?> type="checkbox" name="search_comments" value="1" >
				<?= @text('COM-SEARCH-OPTION-COMMENTS') ?>
		    </label>
		</form>

		<?php
        $url = array('layout'=>'list');
         
        if(!empty($sort))
        	$url['sort'] = $sort;
        	
        if(!empty($scope))
        	$url['scope'] = $scope;
        ?>

		<div id="an-search-results" class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
	    <?= @template('list') ?>
		</div>
	</div>
</div>

