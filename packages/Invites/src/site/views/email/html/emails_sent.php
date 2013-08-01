<?php defined('KOOWA') or die('Restricted access');?>
<?php $payloads = new KConfig($data)?>
<?php foreach($payloads as $i=>$payload) : ?>
<?php if ( $payload->person) : ?>
<span class="help-inline " data-behavior="Element.Inject" data-element-inject-where="after" 
        data-element-inject-container=".control-group:nth-of-type(<?=$i+1?>) .input-prepend">
    <?php if ( $payload->person ) : ?>
      <?= sprintf(@text('COM-INVITES-ALREADY-HERE'), @name($payload->person))?>
    <?php else : ?>
       Sent
    <?php endif;?>
</span>
<?php endif;?>
<?php endforeach;?>
