<?php defined('KOOWA') or die; ?>
<?php 
	if ( empty($update_container) ) {
		$update_container = '#an-entities-main ! div';
	}
?>
<form  data-behavior="Request" data-request-options="{fireSubmitEvent:false,url:'<?=$search_action?>',update:'<?= $update_container ?>'}" id="an-search-form" class="an-filterbox" name="an-search-form"  method="GET">				
	<input  placeholder="<?=@text('LIB-AN-FILTER-PLACEHOLDER')?>" type="text" name="q" class="input-large search-query" id="an-search-query" value="<?=empty($value) ? '' : $value?>" size="21" maxlength="21" />
	
</form>