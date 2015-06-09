<?php defined('KOOWA') or die('Restricted access') ?>

<?php 
$url = empty($comment) ?  $parent->getURL() : $comment->getURL();
$action = empty($comment)  ? 'addcomment' : 'editcomment';
$editor = !isset($editor) ? false : $editor;

if ( $editor ) 
{
    $url .= '&comment[editor]=1';
}
?>

<form id="<?= uniqid() ?>" class="an-comment-form form-stacked an-entity" method="post" action="<?= @route($url) ?>">		
	<input type="hidden" name="action" value="<?= $action ?>" />
	
	<div class="comment-form-avatar">
	<?php if (isset($comment)) : ?>
	<?= @avatar($comment->author)  ?>
	<?php else : ?>
	<?= @avatar(get_viewer())  ?>
	<?php endif;?>
	</div>

	<div class="comment-form-container">
		<div class="control-group">
			<div class="controls">
			    <textarea name="body" cols="5" rows="3" class="input-block-level" required maxlength="5000"><?= isset($comment) ? $comment->getBody() : '' ?></textarea>
			</div>
		</div>

		<div class="comment-actions">
			<?php if(isset($comment)) : ?>			
			<a class="btn action-cancel" data-action="cancelcomment" href="<?= @route($comment->getURL().'&comment[layout]=list')?>">
				<?= @text('LIB-AN-ACTION-CANCEL') ?>
			</a> 
									
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-UPDATING') ?>">
			    <?= @text('LIB-AN-ACTION-UPDATE') ?>
			</button>				
			<?php else : ?>
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
			    <?= @text('LIB-AN-ACTION-POST') ?>
			</button>
			<?php endif; ?>
		</div>
	</div>
</form>