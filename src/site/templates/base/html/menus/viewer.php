<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$num_notifications = empty($num_notifications) ? get_viewer()->numOfNewNotifications() : $num_notifications; 
$viewer = get_viewer();
$components = $this->getService('com://site/people.template.helper')->viewerMenuLinks($viewer);
?>

<ul class="nav pull-right">
	<li>
        <a data-trigger="notifications-popover" href="<?= @route('option=com_notifications&view=notifications&layout=popover&new=1&limit=20') ?>">
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
			
			<?php if(KService::get('koowa:loader')->loadClass('ComGroupsDomainEntityGroup')): ?>
            <li class="divider"></li>
            <li>
            	<a href="<?= @route('option=com_groups&view=groups&oid='.$viewer->uniqueAlias.'&filter=following') ?>">
            	<?= @text('TMPL-MENU-ITEM-VIEWER-GROUPS') ?>
            	</a>
            </li>
            <?php endif; ?>
            
			<?php if(count($components)): ?>
			<li class="divider"></li>
            <?php foreach($components as $component): ?>
            <li>
            	<a href="<?= @route($component->url) ?>">
            	<?= $component->title ?>
            	</a>
            </li>
            <?php endforeach; ?>
            <?php endif; ?>

            <li class="divider"></li>

            <?php if(KService::get('koowa:loader')->loadClass('ComInvitesDomainEntityToken')): ?>
            <li>
            	<a href="<?= @route('option=com_invites&view=email') ?>">
            	<?= @text('TMPL-MENU-ITEM-VIEWER-INVITE') ?>
            	</a>
            </li>
            <?php endif; ?>
            
            <?php if( !$viewer->admin() && KService::get('koowa:loader')->loadClass('ComSubscriptionsDomainEntityOrder')): ?>    
            <li> 
                <a href="<?= @route( 'option=com_subscriptions&view=orders&oid='.$viewer->id ) ?>">
                <?= @text('TMPL-MENU-ITEM-VIEWER-SUBSCRIPTIONS-ORDERS-HISTORY') ?>
                </a>
            </li>
            <?php endif; ?>
            
			<li>
				<a data-trigger="PostLink" href="<?= @route('option=com_people&view=session&action=delete') ?>">
				    <?= @text('LIB-AN-ACTION-LOGOUT') ?>
				</a>
			</li>
		</ul>
	</li>
</ul>