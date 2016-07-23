<? defined('KOOWA') or die('Restricted access'); ?>

<? @title('No navigation bar')?>
<? @description('An example of a page without a navigation bar.') ?>

<? @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>

<h1>No Navbar</h1>

<p>This page doesn't have a navigation bar!</p>
