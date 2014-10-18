<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$num_notifications = empty($num_notifications) ? get_viewer()->numOfNewNotifications() : $num_notifications; 
$viewer = get_viewer();
$components = $this->getService('com://site/people.template.helper')->viewerMenuLinks($viewer);
?>

<ul class="nav pull-right" data-behavior="BS.Dropdown">
	<li>     
		<a href="#" data-popover-tipclass="notifications-popover" data-behavior="RemotePopover" data-bs-popover-animate=false data-bs-popover-content-element="D" data-bs-popover-trigger="click" data-bs-popover-location="bottom" data-remotepopover-url="<?=@route('option=com_notifications&view=notifications&layout=popover')?>" >
            <span id="new-notifications-counter" class="badge <?= ($num_notifications) ? 'badge-important' : '' ?>">
            <?= $num_notifications ?>
			</span>           
        </a>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle">
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
			<li>
				<a data-trigger="Submit" href="<?= @route('option=com_people&view=session&action=delete') ?>">
				<?= @text('LIB-AN-ACTION-LOGOUT') ?>
				</a>
			</li>
		</ul>
	</li>
</ul>

<script data-inline>
var metaTitle = document.getElement('title').innerHTML;

(function(){
    new Request.JSON({
        url : '<?= @route('option=com_notifications&view=notifications&get=count') ?>',
        onSuccess : function(data) {
            var badge = document.id('new-notifications-counter');
            badge.set('text', data.new_notifications);

            if(data.new_notifications){
            	document.getElement('title').innerHTML = '(' + data.new_notifications + ') ' + metaTitle;
            }
                
            if(data.new_notifications > 0){   
                badge.addClass('badge-important');
            }else{
                badge.removeClass('badge-important');   
            }
        }
    }).get();
})
.periodical(30000);
</script> 