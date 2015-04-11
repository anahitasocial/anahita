<?php defined('KOOWA') or die('Restricted access') ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>
<?php $return = empty($return) ? null : $return; ?>

<div class="row">
	<div class="offset3 span6">	
		<?= @template('form', array('connect'=>true)) ?>
	</div>
</div>