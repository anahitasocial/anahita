<?php defined('KOOWA') or die; ?>

<?php if(!empty($keywords)) : ?>
<script src="com_search/js/search_request.js" />
<?php endif;?>
<module position="sidebar-a" style="none">
<?php if ( !empty($keywords)) : ?>
<?= @template('scopes') ?>
<?php endif;?>
</module>

<module position="sidebar-b" style="none"></module>


<?php if(empty($keywords)) : ?>
<form action="<?=@route('view=searches')?>" class="well">
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
	<?php if ( !empty($keywords)) : ?>
	<?= @template('list') ?>
	<?php endif;?>
</div>

