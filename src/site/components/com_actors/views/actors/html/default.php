<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div id="an-actors" class="an-entities">
<?= @template('list') ?>
</div>

<script>
$('#an-actors').infinitscroll({
	url: '<?= @route('layout=list') ?>'
});
</script>