<?php defined('KOOWA') or die ?>

<?php foreach($inputs as $label => $input) : ?>
<div class="control-group">
	<?php if ( is_int(key($inputs)) )  : ?>
		<?php $input = $label ?>
		<?php $label = null?>
	<?php endif;?>
	<?php if($label) : ?>	
		<?php if (strpos($label, '<label') === 0 ) : ?>
			<?= $label ?>
		<?php else :?>
		 	<label class="control-label" for="<?= $label ?>"><?= @text($label) ?></label>
		<?php endif;?>
	<?php endif;?>
	<?php 
	    $input_class = 'input';
	    if ( $input instanceof LibBaseTemplateHelperHtmlElement )
	    {
	        $add_on = '';
	        if ($input->prepend) 
	        {
	            $input        = '<span class="add-on">'.$input->prepend.'</span>'.$input;
	            $input_class .= ' prepend';
	        } 
	        elseif ( $input->append ) 
	        {
	            $input        = $input.'<span class="add-on">'.$input->append.'</span>';
	            $input_class .= ' append';	            
	        }	        
	    }
	?>
	<div class="controls <?= $input_class ?>"><?= $input ?></div>
</div>
<?php endforeach;?> 
