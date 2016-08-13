<? defined('KOOWA') or die;?>

<?
$viewer = get_viewer();
$components = $this->getService('com://site/people.template.helper')->viewerMenuLinks($viewer);
?>

<ul class="nav">
<? if ($viewer->guest()): ?>
	<li>
		<? $return = base64UrlEncode(KRequest::url()); ?>
		<a href="<?= @route('option=com_people&view=session&return='.$return) ?>">
		<?= @text('LIB-AN-ACTION-LOGIN') ?>
		</a>
	</li>
<? else : ?>
	<li>
		<a href="<?=@route($viewer->getURL())?>">
		<?= @avatar(get_viewer(), 'square', false) ?>
		</a>
	</li>
	<li>
		<a href="<?= @route($viewer->getURL().'&get=graph'); ?>">
		<?= @text('TMPL-MENU-ITEM-VIEWER-SOCIALGRAPH') ?>
		</a>
	</li>

<? if (KService::get('koowa:loader')->loadClass('ComGroupsDomainEntityGroup')): ?>
    <li class="divider"></li>
    <li>
    	<a href="<?= @route('option=com_groups&view=groups&oid='.$viewer->uniqueAlias.'&filter=following') ?>">
        <?= @text('TMPL-MENU-ITEM-VIEWER-GROUPS') ?>
        </a>
    </li>
 	<? endif; ?>

	<? if (count($components)): ?>
	<li class="divider"></li>
    <? foreach ($components as $component): ?>
    <li>
    	<a href="<?= @route($component->url) ?>">
    	<?= $component->title ?>
    	</a>
    </li>
    <? endforeach; ?>
    <? endif; ?>
    <li class="divider"></li>
	<li>
		<a data-trigger="PostLink" href="<?= @route('option=com_people&view=session&action=delete') ?>">
        <?= @text('LIB-AN-ACTION-LOGOUT') ?>
		</a>
	</li>
<? endif; ?>
</ul>
