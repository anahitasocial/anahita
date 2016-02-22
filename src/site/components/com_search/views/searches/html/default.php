<?php defined('KOOWA') or die; ?>

<?php if (!empty($keywords)) : ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_search/js/search.js" />
<?php else: ?>
<script src="com_search/js/min/search.min.js" />
<?php endif; ?>

<?= @map_api_nearby(array()) ?>
<?= @map_api(array('libraries'=>'places')) ?>

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
        <label name="SortOptions"><?= @text('COM-SEARCH-OPTION-SORT') ?></label>
        <select form="navbar-search" data-trigger="SortOption" id="SortOptions" name="sort">
    				<option <?= ($sort == 'relevant') ? 'selected' : '' ?> value="relevant">
    					<?= @text('COM-SEARCH-OPTION-SORT-MOST-RELEVANT') ?>
    				</option>

    				<option <?= ($sort == 'recent') ? 'selected' : '' ?> value="recent">
    					<?= @text('COM-SEARCH-OPTION-SORT-MOST-RECENT') ?>
    				</option>

            <option <?= ($sort == 'distance') ? '' : 'disabled' ?> <?= ($sort == 'distance') ? 'selected' : '' ?> value="distance">
    					<?= @text('COM-SEARCH-OPTION-SORT-DISTANCE') ?>
    				</option>
  			</select>

        <label name="SearchNearby"><?= @text('COM-SEARCH-OPTION-NEARBY') ?></label>
        <input form="navbar-search" type="text" id="SearchNearby" data-trigger="SearchNearby" name="search_nearby" placeholder="<?= @text('COM-SEARCH-OPTION-NEARBY-PLACEHOLDER') ?>" />

        <?php $ranges = array(100,50,25,10,5); ?>
        <select form="navbar-search" disabled id="SearchRange" data-trigger="SearchRange" name="search_range" class="input-small">
            <?php foreach($ranges as $index=>$range) : ?>
            <option <?= ($range === 25) ? 'selected' : '' ?> value="<?= $range ?>"><?= $range ?> km</option>
            <?php endforeach; ?>
        </select>

        <label class="checkbox">
            <input form="navbar-search" data-trigger="SearchOption" <?= $search_comments ? 'checked' : ''?> type="checkbox" name="search_comments" value="1" >
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

    <?= @infinitescroll($items, array(
      'url' => $url,
      'id' => 'an-search-results'
    )) ?>
	</div>
</div>
