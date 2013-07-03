<?php defined('KOOWA') or die ?>

<?php
$url = ''; 
if ( strlen($gadget->url) ) {    
    $url = "'url':'".@route($gadget->url)."',";
}

?>
<?php $id = uniqid() ?>

<div data-behavior="Load"  data-load-options="{<?=$url?>'element':'.gadget-content'}" class="an-gadget <?=$gadget->id?>">
    <?php if ( $gadget->show_title !== false) : ?>     
    	<?php if (strlen($gadget->title) ) : ?>
    	<h2 class="gadget-title">
            <?php if ($gadget->title_url) : ?>
                <a href="<?= @route($gadget->title_url) ?>">
                    <?=$gadget->title?>
                </a>
            <?php else : ?>
                <?=$gadget->title?>
            <?php endif;?>
            
            <?php if (strlen($gadget->action) && $gadget->action_url ) : ?>
	        <a class="gadget-action" href="<?= @route($gadget->action_url) ?>">
            	<?= $gadget->action?>
	        </a>    
	        <?php endif; ?>
    	</h2>    	
    	<?php endif; ?> 
	<?php endif;?>
	
	<div class="gadget-content">    
	<?= $gadget->content ?>
	</div>		
</div>


