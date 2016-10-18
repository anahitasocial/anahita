<? defined('KOOWA') or die; ?>

<form action="<?= $search_action ?>" id="an-filterbox" class="form-inline" name="an-filterbox"  method="get">
	<input placeholder="<?= $placeholder ?>" type="text" name="q" class="input-large search-query" id="an-search-query" value="<?= empty($value) ? '' : $value ?>" size="50" maxlength="50" />
</form>
