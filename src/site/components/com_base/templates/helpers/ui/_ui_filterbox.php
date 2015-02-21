<?php defined('KOOWA') or die; ?>

<form action="<?= $search_action ?>" id="an-filterbox" class="an-filterbox" name="an-filterbox"  method="get">				
	<input placeholder="<?= @text('LIB-AN-FILTER-PLACEHOLDER') ?>" type="text" name="q" class="input-large search-query" id="an-search-query" value="<?= empty($value) ? '' : $value ?>" size="21" maxlength="21" />
</form>