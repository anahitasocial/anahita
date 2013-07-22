<?php if (!defined('KOOWA')) die; ?>

<div class="page-header">
    <h1><?= $error->code ?> - <?php print $error->message ?></h1>
</div>

<div class="alert alert-block alert-error">
<p><?= $error->message ?></p>
</div>