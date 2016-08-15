<? defined('KOOWA') or die('Restricted access');?>

<div class="btn-toolbar clearfix visible-desktop">
    <div class="pull-left visible-desktop">
        <?= @helper('ui.commands', $toolbar->getCommands()->class('btn btn-primary')) ?>
    </div>
</div>
