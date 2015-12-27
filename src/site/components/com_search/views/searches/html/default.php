<?php defined('KOOWA') or die; ?>

<?php if (!empty($keywords)) : ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_search/js/search.js" />
<script src="com_locations/js/nearby.google.js" />
<?php else: ?>
<script src="com_search/js/min/search.min.js" />
<script src="com_locations/js/min/nearby.google.min.js" />
<?php endif; ?>

<script src="https://maps.googleapis.com/maps/api/js?libraries=places" />

<?php endif;?>

<div class="row">
    <div class="span4">
    <?php if (!empty($keywords)): ?>
    <?= @template('scopes') ?>
    <?php endif;?>
    </div>

	<div class="span8">

		<?= @helper('ui.header', array()) ?>

    <fieldset>
        <label name="nearby"><?= @text('COM-SEARCH-OPTION-NEARBY') ?></label>
        <input type="text" id="SearchNearby" data-trigger="SearchNearby" name="search_nearby" placeholder="<?= @text('COM-SEARCH-OPTION-NEARBY-PLACEHOLDER') ?>" />

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
   </fieldset>

		<?php
        $url = array('layout' => 'list');

        if (!empty($sort)) {
            $url['sort'] = $sort;
        }

        if (!empty($scope)) {
            $url['scope'] = $scope;
        }
        ?>

		<div id="an-search-results" class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
	    <?= @template('list') ?>
		</div>
	</div>
</div>
