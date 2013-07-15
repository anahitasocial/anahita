<?php $route = KRequest::url() ?>
<a class="btn btn-primary" data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=people&view=session&layout=modal&return='.base64_encode($route))?>" >
    <?= @text('MOD-VIEWER-LOGIN') ?>                                               
</a>