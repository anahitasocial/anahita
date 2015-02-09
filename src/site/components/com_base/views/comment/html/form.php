<?php defined('KOOWA') or die('Restricted access') ?>

<?php 
$url = empty($comment) ?  $parent->getURL() : $comment->getURL();
$action = empty($comment)  ? 'addcomment' : 'editcomment';
$editor = !isset($editor) ? false : $editor;

if ( $editor ) {
    $url .= '&comment[editor]=1';
}
?>

<form id="<?= uniqid() ?>" class="an-comment-form form-stacked" method="post" action="<?= @route($url) ?>">		
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
			<?php if ( $editor ) : ?>			
			<?php $id   = isset($comment) ? $comment->id   : time() ?>
			<?php $body = isset($comment) ? $comment->getBody() : ''?>
			<?= @editor(array('name'=>'body', 'content'=>$body, 'html'=>array('cols'=>50, 'rows'=>5, 'class'=>'input-block-level','required'=>'required', 'maxlength'=>'5000', 'id'=>'an-comment-body-'.$id)))?>
			<?php else : ?>
			<textarea name="body" cols="50" rows="3" class="input-block-level" required maxlength="5000"><?= isset($comment) ? $comment->getBody() : '' ?></textarea>
			<?php endif;?>
			</div>
		</div>

		<div class="comment-actions">
			<?php if(isset($comment)) : ?>
			<button type="button" class="btn action-cancel"  name="cancel" data-url="<?= @route($comment->getURL().'&comment[layout]=list&comment[editor]='.$editor)?>">
			    <?= @text('LIB-AN-ACTION-CANCEL') ?>
			</button>								
			<button type="submit" class="btn btn-primary">
			    <?= @text('LIB-AN-ACTION-UPDATE') ?>
			</button>				
			<?php else : ?>
			<button type="submit" class="btn btn-primary">
			    <?= @text('LIB-AN-ACTION-POST') ?>
			</button>
			<?php endif; ?>
		</div>
	</div>
</form>