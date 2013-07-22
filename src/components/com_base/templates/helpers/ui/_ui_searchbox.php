<?php defined('KOOWA') or die; ?>
<?php 
	if ( empty($update_container) ) {
		$update_container = '#an-entities-main ! div';
	}
?>
<form id="an-search-form" class="well form-search" name="an-search-form"  method="GET">				
	<input  type="text" name="q" class="input-large search-query" id="an-search-query" value="" size="21" maxlength="21" />
	<button data-trigger="Request" data-request-options="{url:'<?=$search_action?>',update:'<?= $update_container ?>'}" type="submit" class="btn">
		<?=@text('LIB-AN-ACTION-SEARCH')?>
	</button>
</form>