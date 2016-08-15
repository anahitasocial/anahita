<? defined('KOOWA') or die('Restricted access');?>	

<div class="an-entities" id="an-entities-main">
<? if (count($photos)) : ?>
	<? foreach ($photos as $photo) : ?>
	<?= @view('photo')->layout('list')->photo($photo)->filter($filter) ?>
	<? endforeach; ?>
<? else: ?>
	<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
</div>

<?= @pagination($photos, array('url' => @route('layout=list'))) ?>
