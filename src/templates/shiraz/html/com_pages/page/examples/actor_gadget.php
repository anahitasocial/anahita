<? defined('ANAHITA') or die('Restricted access'); ?>


<div class="row">
	<div class="span4">
		<h3 class="block-title">Person Gadget</h3>
		<div class="block-content">
		<?= @service('com://site/people.controller.person')->limit(10)->view('people')->layout('badge'); ?>
		</div>
	</div>
</div>
