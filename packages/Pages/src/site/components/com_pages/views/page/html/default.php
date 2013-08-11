<?php defined('KOOWA') or die; ?>

<?php if ($actor->authorize('administration')) : ?>
<module position="sidebar-b" title="<?= @text('COM-PAGES-PAGE-PRIVACY') ?>">
	<?= @helper('ui.privacy',$page) ?>
</module>
<?php endif; ?>

<module position="sidebar-b" title="<?= @text('COM-PAGES-META-ADDITIONAL-INFORMATION') ?>">
	<ul class="an-meta" >
		<li><span class="label label-info"><?= @text('COM-PAGES-PAGE-REVISION-META-CURRENT') ?></span></li>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($page->updateTime), @name($page->editor)) ?></li>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $page->numOfComments) ?></li>
	</ul>
</module>

<?= @template('page') ?>

<?= @helper('ui.comments', $page, array('pagination'=>true)) ?>