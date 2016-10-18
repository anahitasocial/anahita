<? defined('KOOWA') or die('Restricted access') ?>

<? @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<? $return = empty($return) ? null : $return; ?>

<div class="row">
	<div class="offset3 span6">
		<?= @template('form', array('connect' => true)) ?>
	</div>
</div>
