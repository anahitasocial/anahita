<? defined('KOOWA') or die; ?>

<? $document = KService::get('anahita:document'); ?>
<? $path = KRequest::base().'/media/lib_anahita/js/production/' ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>

<?
@helper('javascript.combine', array(
    'file' => ANPATH_ROOT.'/media/lib_anahita/js/site.js',
    'output' => ANPATH_ROOT.'/media/lib_anahita/js/production/site.uncompressed.js',
));

$document->addScript($path.'site.uncompressed.js');
?>
<? else : ?>
<? $document->addScript($path.'site.js'); ?>
<? endif; ?>
<?
$document->addScript('https://cdn.plyr.io/2.0.11/plyr.js');
$document->addStyleSheet('https://cdn.plyr.io/2.0.11/plyr.css');
?>
