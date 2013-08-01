<?php 

?>
<module position="sidebar-b"></module>
<p>
    <?= sprintf(@text('COM-INVITES-INVITED-BY'), @name($token->inviter))?>
</p>


<style>
#inviter-list #block {
    display:none;
}
</style>
<div id="inviter-list">
<?= @service('com://site/people.controller.person')
    ->setItem($token->inviter)
    ->layout('list'); ?>
</div>    
<?php if ( $viewer->guest() ) : ?>
<a class="btn btn-large" data-trigger="BS.showPopup" data-bs-showpopup-url="<?=@route('option=people&view=session&layout=modal&return='.base64_encode(@route('token='.$token->value)))?>" >
<?= @text('COM-INVITES-LOGIN')?>
</a>

<a class="btn btn-large btn-primary" data-trigger="BS.showPopup" data-bs-showpopup-url="<?=@route('option=people&view=person&modal=1&layout=add&return='.base64_encode(@route('token='.$token->value)))?>" >
<?= @text('COM-INVITES-SIGN-UP')?>
</a>
<?php endif;?>