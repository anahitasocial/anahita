<?php defined('KOOWA') or die('Restricted access'); ?>

<?php @title('No navigation bar')?>
<?php @description('An example of a page without a navigation bar.') ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<h1>No Navbar</h1>

<p>This page doesn't have a navigation bar!</p>
