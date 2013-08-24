<?php defined('KOOWA') or die('Restricted access'); ?>


<div class="row">
	<div class="span4">
		<h3 class="module-title">Person Gadget</h3>
		<div class="module-content">
		<?= @service('com://site/people.controller.person')->ids(array(1))->view('people')->layout('badge'); ?>
		</div>
	</div>
</div>