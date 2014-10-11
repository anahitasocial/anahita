<?php defined('KOOWA') or die; ?>

<?php if(!empty($keywords)) : ?>
<script src="com_search/js/search_request.js" />
<?php endif;?>

<div class="row">
	<div class="span8">	
		<?= @helper('ui.header', array()) ?>
	
		<?php if(!empty($keywords) && $items->getTotal()): ?>
		<form>
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
		<?php endif;?>
				
		<?php if(empty($keywords)) : ?>
		<form action="<?= @route('view=searches') ?>" class="well">
			<fieldset>
				<legend><?= @text('COM-SEARCH-PROMPT') ?></legend>
				<input type="text" name="term" class="input-block-level">
				
				<label class="checkbox">
					<input type="checkbox" name="search_comments" value="1" >
					<?= @text('COM-SEARCH-OPTION-COMMENTS') ?>
		    	</label>
			</fieldset>
			<div class="form-actions">
				<button type="submit" name="submit" class="btn btn-primary btn-large">
					<?= @text('LIB-AN-ACTION-SEARCH') ?>
				</button>
			</div>
		</form>
		<?php endif ?>

		<div class="an-entities-wrapper">
		<?= @template('list') ?>
		</div>
	</div>
	
	<div class="span4">
	<?php if(!empty($keywords)): ?>
	<?= @template('scopes') ?>
	<?php endif;?>
	</div>
</div>

