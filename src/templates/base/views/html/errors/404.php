<?php if (!defined('KOOWA')) die; ?>

<div class="page-header">
	<h1><?php print $error->code ?> - <?php print $error->message ?></h1>
</div>

<div class="alert alert-block alert-error">
<h4 class="alert-heading"><?php print JText::_('TMPL-ERROR-404-TITLE') ?></h4>
<p><?php print JText::_('TMPL-ERROR-404-DESC') ?></p>
</div>
