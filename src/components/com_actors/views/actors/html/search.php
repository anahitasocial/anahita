<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="none"></module>

<?= @helper('ui.searchbox', @route('layout=list'), array('update_container'=>'.an-entities-wrapper'))?>

<div class="an-entities-wrapper">

</div>