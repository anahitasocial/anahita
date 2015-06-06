<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
    <div class="span8">
        <?= @helper('ui.header', array()) ?>
    
        <div id="entity-form-wrapper" class="hide">
        <?= @view('coupon')->layout('form') ?>
        </div>
    
        <?= @helper('ui.filterbox', @route('layout=list')) ?>
        <?= @template('list') ?>
    </div>
</div>