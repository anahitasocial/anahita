<?php defined('KOOWA') or die('Restricted access');?>

<module position="sidebar-b"></module>

<?php if($actor && $actor->id > 0 && empty($filter)): ?>
	<?php $sets = $actor->sets->order('updateTime', 'DESC')->limit(20); ?>
	<?php if(count($sets)): ?>
	<module position="sidebar-b" title="<?= @text('COM-PHOTOS-MODULE-HEADER-SETS') ?>">
		<?= @controller('sets')->view('sets')->oid($actor->id)->layout('module') ?>	
	</module>
	<?php endif; ?>
<?php endif; ?>

<div class="an-entities-wrapper" id="an-entities-main-wrapper">
<?= @template('list') ?>
</div>

