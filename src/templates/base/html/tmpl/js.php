<? defined('KOOWA') or die; ?>

<? $document = KService::get('com:document')->getInstance(); ?>
<? $path = KRequest::base().'/media/lib_anahita/js/production/' ?>

<? if (defined('JDEBUG') && JDEBUG) : ?>

<?
@helper('javascript.combine', array(
    'file' => JPATH_ROOT.'/media/lib_anahita/js/site.js',
    'output' => JPATH_ROOT.'/media/lib_anahita/js/production/site.uncompressed.js',
));

$document->addScript($path.'site.uncompressed.js');
?>
<? else : ?>
<? $document->addScript($path.'site.js'); ?>
<? endif; ?>
