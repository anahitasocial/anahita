<?php defined('KOOWA') or die; ?>

<div class="an-meta">
	<p><?= sprintf(@text('COM-SEARCH-RESULTS-FOUND'), $items->getTotal()) ?></p>
</div>

<?php if ( $current_scope && ($current_scope->commentable || $current_scope->ownable) && $items->getTotal() ) : ?>
<form>
	<?php if ( $current_scope->ownable ) : ?>
	<select data-trigger="SortOption" name="sort">
		<option <?= ($sort == 'relevant') ? 'selected' : '' ?> value="relevant">
			<?= @text('COM-SEARCH-OPTION-SORT-MOST-RELEVANT') ?>
		</option>
		<option <?= ($sort == 'recent') ? 'selected' : '' ?> value="recent">
			<?= @text('COM-SEARCH-OPTION-SORT-MOST-RECENT') ?>
		</option>
	</select>
	<?php endif;?>
	
	<?php if ($current_scope->commentable) : ?>
	<label class="checkbox">
		<input data-trigger="SearchOption" <?= $search_comments ? 'checked' : ''?> type="checkbox" name="search_comments" value="1" >
		<?= @text('COM-SEARCH-OPTION-COMMENTS') ?>
    </label>
    <?php endif;?>
</form>
<?php endif;?>

<div class="an-entities" id="an-entities-main">
<?php if(isset($keywords)): ?>
	<?php if(count($items)) :?>
		<?php foreach($items as $item ) : ?>
			<?= @view('search')->layout('list')->item($item)->keywords($keywords)?>
		<?php endforeach; ?>
	<?php else : ?>
		<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
	<?php endif; ?>
<?php endif; ?>
</div>

<?php if(isset($keywords)): ?>
<?= @pagination($items, array('url'=>@route('layout=list&term='.$term))) ?>
<?php endif; ?>