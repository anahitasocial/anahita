<?php defined('KOOWA') or die ?>

<module position="sidebar-b" type="none"></module>

<?= @template('milestone') ?>

<?= @helper('ui.comments', $milestone, array('pagination'=>true)) ?>