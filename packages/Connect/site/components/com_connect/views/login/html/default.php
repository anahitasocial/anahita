<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="none"></module>

<h4>
<?= sprintf(@text('COM-CONNECT-LOGIN-PROMPT-HI'), $api->getUser()->name, ucfirst($api->getName())) ?>
</h4>

<?php if ( @service('com://site/people.controller.person')->canRegister() ) : ?>

<h4>
<?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN-OR-SIGNUP')?>
</h4>
<div>    
    <button data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=session&layout=modal_simple&ajax=1') ?>" class="btn btn-large"><?= @text('COM-CONNECT-ACTION-MAP-ACCOUNT')?></button>
    <button data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=person&layout=add&modal=1') ?>" class="btn btn-large"><?= @text('COM-CONNECT-ACTION-SIGNUP')?></button>
</div>
<?php else : ?>
<h4><?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN')?></h4>
<button data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=com_people&view=session&layout=modal_simple&ajax=1') ?>" class="btn btn-large"><?= @text('COM-CONNECT-LOGIN-PROMPT-LOGIN')?></button>
<?php endif;?>
