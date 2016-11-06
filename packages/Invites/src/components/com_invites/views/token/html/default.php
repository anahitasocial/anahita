<? defined('KOOWA') or die('Restricted access');?>

<div class="alert alert-block alert-success">
    <p><?= sprintf(@text('COM-INVITES-INVITED-BY'), @name($token->inviter)) ?></p>
    <p>
    <? if ($viewer->guest()): ?>
    <a class="btn btn-primary" href="<?= @route('option=people&view=person&layout=signup') ?>" >
    <?= @text('COM-INVITES-SIGN-UP') ?>
    </a>
    <? endif; ?>
    </p>
</div>

<div class="an-entities masonry">
<?= @service('com:people.controller.person')->setItem($token->inviter)->layout('list'); ?>
</div>

<p></p>
