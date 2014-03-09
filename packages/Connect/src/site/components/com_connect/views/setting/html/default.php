<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-CONNECT-PROFILE-SETTING-TITLE') ?></h3>

<div class="an-entities">
<?php foreach($apis as $api) : ?>
<?php 
$session = $sessions->find(array('api'=>$api->getName()));
if ( $session && !$session->validateToken() ) 
{
	$session->delete()->save();
    $session = null;
}
?>
	<div class="an-entity connect-type-<?= $api->getName() ?>">
		<h4 class="entity-title">
			<?= @text(ucfirst($api->getName())) ?> 
            <?php if (!empty($session) ) : ?>
            - <small><?= ($api->getName() == 'twitter') ? '@' : '' ?><?= pick($session->api->getUser()->username, $session->api->getUser()->name) ?></small>
            <?php endif; ?>
        </h4>
            
		<div class="entity-description">
			<?= @text('COM-CONNECT-API-DESC-'.strtoupper($api->getName())) ?>
		</div>		
            <?php                 
                if ( $session && !$session->validateToken() ) 
                {
                    $session->delete()->save();
                    $session = null;
                }
            ?>
		<div class="entity-actions">
			<?php if ( !$session ) : ?>		
			<a class="btn btn-primary" data-trigger="Submit" href="<?= @route(array('option'=>'com_connect','view'=>'setting','oid'=>$actor->uniqueAlias, 'get'=>'accesstoken', 'server'=>$api->getName()))?>">
				<?= @text('LIB-AN-ACTION-ENABLE')?>
			</a>
			<?php else : ?>
			<a class="btn" data-trigger="Submit" href="<?= @route(array('option'=>'com_connect','view'=>'setting','oid'=>$actor->uniqueAlias, '_action'=>'delete', 'server'=>$api->getName()))?>">
				<?= @text('LIB-AN-ACTION-DISABLE')?>
			</a>				
			<?php endif;?>
		</div>
	</div>		
<?php endforeach; ?> 
</div>
