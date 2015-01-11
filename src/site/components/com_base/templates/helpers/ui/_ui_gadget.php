<?php defined('KOOWA') or die ?>

<?php $url = (strlen($gadget->url)) ? @route($gadget->url) : ''; ?>

<div class="an-gadget" data-url="<?= $url ?>">
    <?php if ( $gadget->show_title !== false) : ?>     
    	<?php if (strlen($gadget->title) ) : ?>
    	<h3 class="gadget-title">
            <?php if ($gadget->title_url) : ?>
            <a href="<?= @route($gadget->title_url) ?>">
                <?=$gadget->title?>
            </a>
            <?php else : ?>
            <?=$gadget->title?>
            <?php endif;?>
            
            <?php if (strlen($gadget->action) && $gadget->action_url ) : ?>
	        <a class="gadget-action" href="<?= @route($gadget->action_url) ?>">
            <?= $gadget->action ?>
	        </a>    
	        <?php endif; ?>
    	</h3>    	
    	<?php endif; ?> 
	<?php endif;?>
	
	<div class="gadget-content">
	    <?= $gadget->content ?>
	</div>		
</div>


