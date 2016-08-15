<? defined('KOOWA') or die ?>

<? foreach ($inputs as $label => $input) : ?>
<div class="control-group">
	<? if (is_int(key($inputs)))  : ?>
		<? $input = $label ?>
		<? $label = null?>
	<? endif;?>
	<? if ($label) : ?>
		<? if (strpos($label, '<label') === 0) : ?>
			<?= $label ?>
		<? else :?>
		 	<label class="control-label" for="<?= $label ?>"><?= @text($label) ?></label>
		<? endif;?>
	<? endif;?>
	<?
        $input_class = 'input';
        if ($input instanceof LibBaseTemplateHelperHtmlElement) {
            $add_on = '';
            if ($input->prepend) {
                $input = '<span class="add-on">'.$input->prepend.'</span>'.$input;
                $input_class .= ' prepend';
            } elseif ($input->append) {
                $input = $input.'<span class="add-on">'.$input->append.'</span>';
                $input_class .= ' append';
            }
        }
    ?>
	<div class="controls <?= $input_class ?>"><?= $input ?></div>
</div>
<? endforeach;?>
