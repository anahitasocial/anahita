<?php if (!defined('KOOWA')) {
    die;
} ?>

<div class="page-header">
	<h1><?= @text('TMPL-ERROR-404-HEADER') ?></h1>
</div> 

<div class="alert alert-block alert-error">
<h4 class="alert-heading"><?php print AnTranslator::_('TMPL-ERROR-404-TITLE') ?></h4>
<p><?php print AnTranslator::_('TMPL-ERROR-404-DESC') ?></p>  
</div>
