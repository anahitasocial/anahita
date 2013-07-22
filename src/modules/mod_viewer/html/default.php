<?php defined('KOOWA') or die('Restricted access');?>

<?php $num_notifications = empty($num_notifications) ? get_viewer()->numOfNewNotifications() : $num_notifications; ?>

<ul class="menu nav" data-behavior="BS.Dropdown">
     <li>     
        <a data-popover-tipclass="notifications-popover" data-behavior="RemotePopover" data-bs-popover-trigger="click" data-bs-popover-location="bottom" data-remotepopover-url="<?=@route('option=com_notifications&view=notifications&layout=popover')?>" href="#" data-trigger="">
            <span id="new-notifications-counter" class="badge <?= ($num_notifications) ? 'badge-important' : '' ?>"><?= $num_notifications ?></span>           
         </a>
     </li>
     <li class="dropdown">
        <a href="#" class="dropdown-toggle">
            <?= @avatar(get_viewer(), 'square', false) ?>&nbsp;
            <?= get_viewer()->name ?>
             <b class="caret"></b>           
         </a>
         <ul class="dropdown-menu">
            <li><a href="<?=@route(get_viewer()->getURL())?>"><?= @text('MOD-VIEWER-MENU-PROFILE') ?></a></li>
            <?php foreach($menus as $menu) : ?>
                <?= $menu->toString() ?>
            <?php endforeach; ?>
             <li class="divider"></li> 
                 
             <li><a href="<?=@route(get_viewer()->getURL().'&get=settings')?>"><?=@text('MOD-VIEWER-MENU-EDIT-PROFILE')?></a></li>                     
             <li><a data-trigger="Submit" href="<?=JRoute::_('index.php?option=com_user&task=logout&return='.$return)?>"><?=@text('MOD-VIEWER-MENU-LOGOUT')?></a></li>
         </ul>
     </li>
 </ul>
<script data-inline>
(function(){
    new Request.JSON({
        url       : '<?= @route('option=com_notifications&view=notifications&get=count')?>',
        onSuccess : function(data) {
            var badge = document.id('new-notifications-counter');
            badge.set('text', data.new_notifications);
            if ( data.new_notifications > 0 ) {   
                badge.addClass('badge-important');
            } else {
                badge.removeClass('badge-important');   
            }
        }
    }).get();
})
.periodical(30000);
</script> 