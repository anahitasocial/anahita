<?php defined('KOOWA') or die('Access Denied') ?>

<?php
$exts = array(); 
foreach($extensions as $ext)
    $exts[] = 'ext['.$ext->getId().']='.$ext->version;
$url = 'src='.urlencode(KRequest::url()).'&'.implode('&', $exts);
if ( !in_array('curl', get_loaded_extensions()) ) 
    $url .= '&no_curl_support=1';
?>
<script src="<?= $store->getHost() ?>/media/com_bzserver/js/cross.js?<?=$url?>" />
<?php $url = $store->getListURL().'&'.$url; ?>
<?php if ( !empty($install_message) )  : ?>
<div class="mc-form-frame mc-padding mc-first-block">
<?= $install_message ?>
</div>
<?php endif;?>
<iframe style="height:2200px; width:1080px;" scrolling="no" frameborder="0" src="<?= $url?>"></iframe>
