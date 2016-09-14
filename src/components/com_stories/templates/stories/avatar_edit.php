<? defined('KOOWA') or die('Restricted access');?>

<?
$targets = $target;

if (!is_array($targets)) {
    $targets = array($targets);
}
?>

<data name="title">
<? if (count($targets) == 1): ?>
<?= sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATAR'), @name($subject)) ?>
<? else: ?>
<?= sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATARS'), @name($subject)) ?>
<? endif; ?>
</data>

<data name="body">
	<? if (count($targets) == 1): ?>
	<?= @avatar($target, 'medium') ?>
	<? else: ?>
	<div class="media-grid">
		<? foreach ($targets as $target) : ?>
		<div><?= @avatar($target, 'square') ?></div>
		<? endforeach; ?>
	</div>
	<? endif; ?>
</data>
