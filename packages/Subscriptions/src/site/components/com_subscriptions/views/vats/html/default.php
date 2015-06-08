<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
    <div class="span8">
        <?= @helper('ui.header', array()) ?>
    
        <div id="entity-form-wrapper" class="hide">
        <?= @view('vat')->layout('form') ?>
        </div>
    
        <?= @template('list') ?>
    </div>
</div>