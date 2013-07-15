<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( !empty($menubar) ) : ?>
<?= @helper('ui.menubar', array('menubar'=>$menubar))?>
<?php endif;?>
<?php if ( !empty($actorbar) ) : ?>
<?= @helper('ui.actorbar', array('actorbar'=>$actorbar))?>
<?php endif;?>
<?php if ( !empty($toolbar) ) : ?>
<?= @helper('ui.toolbar', array('toolbar'=>$toolbar))?>
<?php endif;?>