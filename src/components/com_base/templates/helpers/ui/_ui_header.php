<? defined('KOOWA') or die('Restricted access') ?>

<? if (!empty($menubar)) : ?>
<?= @helper('ui.menubar', array('menubar' => $menubar))?>
<? endif;?>

<? if (!empty($actorbar)) : ?>
<?= @helper('ui.actorbar', array('actorbar' => $actorbar))?>
<? endif;?>

<? if (!empty($toolbar)) : ?>
<?= @helper('ui.toolbar', array('toolbar' => $toolbar))?>
<? endif;?>
