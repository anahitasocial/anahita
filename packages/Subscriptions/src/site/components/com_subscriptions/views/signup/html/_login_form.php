<?php defined('KOOWA') or die('Restricted access');?>

<script data-inline>
document.addEvent('click:relay(#login-button)', function(e){
    e.stop();
    var form = this.form;
    form.spin();
    new Request.JSON({
        url   : 'index.php/people/session',
        data  : form,
        onSuccess : function() { },
        onComplete : function() { form.unspin() },
        on401 : function() { },
        on500 : function() { }
    }).post();
    return;	
});
</script>

<form data-behavior="FormValidator" action="<?=@route()?>" method="post" name="sub-login">
	<fieldset>
		<legend><?= @text('COM-SUB-PLEASE-LOGIN') ?></legend>
			<?php if ( empty($hide_message) ) : ?>
			<div class="alert alert-info">
				<?= @text('COM-SUB-ALREADY-HAVE-ACCOUNT') ?>
			</div>
			<?php endif; ?>
			<?= @helper('ui.form', array(
				'USERNAME OR EMAIL' => @html('textfield', 	  'username', $username)->dataValidators(''),
				'PASSWORD'	    	=> @html('passwordfield', 'passwd',   $password)->dataValidators('')
			))?>
			<div class="form-actions">
				<button type="submit" id="login-button" class="btn btn-primary"><?=@text('COM-SUB-LOGIN')?></button>
			</div>			
	</fieldset>
</form>	
	