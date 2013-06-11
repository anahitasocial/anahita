<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<module position="sidebar-b" style="none"></module>

<?= @template('note') ?>

<?= @helper('ui.comments', $note) ?>