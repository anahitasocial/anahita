<?php defined('KOOWA') or die;?>

<div id="an-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="anModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="anModalLabel"></h3>
  </div>
  <div class="modal-body"></div>
  <div class="modal-footer">
    <a class="btn" data-dismiss="modal" aria-hidden="true"><?= @text('LIB-AN-ACTION-CANCEL') ?></a>
  </div>
</div>