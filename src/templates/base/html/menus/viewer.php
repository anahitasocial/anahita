<? defined('KOOWA') or die('Restricted access');?>

<?
$num_notifications = empty($num_notifications) ? get_viewer()->numOfNewNotifications() : $num_notifications;
$viewer = get_viewer();
$components = $this->getService('com:people.template.helper')->viewerMenuLinks($viewer);
?>

<ul class="nav pull-right">
	<li>
        <a data-trigger="notifications-popover" href="<?= @route('option=com_notifications&view=notifications&layout=popover&limit=20') ?>">
            <span data-url="<?= @route('option=com_notifications&view=notifications&get=count') ?>" data-interval="30000" id="notifications-counter" class="badge <?= ($num_notifications) ? 'badge-important' : '' ?>">
            <?= $num_notifications ?>
			</span>
        </a>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" class="dropdown-toggle" data-toggle="dropdown">
		<?= @avatar(get_viewer(), 'square', false) ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?=@route($viewer->getURL())?>">
				<?= @text('TMPL-MENU-ITEM-VIEWER-PROFILE') ?>
				</a>
			</li>
			<li>
				<a href="<?=@route($viewer->getURL(false).'&get=settings')?>">
				<?=@text('TMPL-MENU-ITEM-VIEWER-PROFILE-EDIT')?>
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

            <? if ($viewer->admin()): ?>
            <li>
                <a href="<?= @route('option=com_people&view=people') ?>">
                    <?= @text('TMPL-MENU-ITEM-VIEWER-PEOPLE') ?>
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

            <? if (KService::get('koowa:loader')->loadClass('ComInvitesDomainEntityToken')): ?>
            <li>
            	<a href="<?= @route('option=com_invites&view=email') ?>">
            	<?= @text('TMPL-MENU-ITEM-VIEWER-INVITE') ?>
            	</a>
            </li>
			<li class="divider"></li>
            <? endif; ?>

            <? if (KService::get('koowa:loader')->loadClass('ComSubscriptionsDomainEntityOrder')) : ?>
            <li>
                 <? if ($viewer->admin()): ?>
                 <a href="<?= @route('option=com_subscriptions&view=orders') ?>">
                 <? else: ?>
                 <a href="<?= @route('option=com_subscriptions&view=orders&oid='.$viewer->id) ?>">
                 <? endif; ?>
                 <?= @text('TMPL-MENU-ITEM-VIEWER-SUBSCRIPTIONS-ORDERS-HISTORY') ?>
                 </a>
            </li>
			<li class="divider"></li>
            <? endif; ?>

			<? if($viewer->superadmin()): ?>
			<li>
				<a href="<?= @route('option=com_settings&view=settings') ?>">
					<?= @text('TMPL-MENU-ITEM-VIEWER-SITE-SETTINGS') ?>
				</a>
			</li>
			<li class="divider"></li>
			<? endif; ?>
			<li>
				<a data-trigger="PostLink" href="<?= @route('option=com_people&view=session&action=delete') ?>">
				    <?= @text('LIB-AN-ACTION-LOGOUT') ?>
				</a>
			</li>
		</ul>
	</li>
</ul>
