<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-CONNECT-PROFILE-SETTING-TITLE') ?></h3>

<div class="an-entities">
<?php foreach ( $apis as $api ) : ?>
    
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
		<div>
    		<form action="<?= @route( array( 'option' => 'com_connect', 'view' => 'setting', 'oid' => $actor->uniqueAlias, 'server' => $api->getName())) ?>" method="post">
    			<?php if ( !$session ) : ?>	
    			<input type="hidden" name="get" value="accesstoken" />
    			<?php else : ?>
    			<input type="hidden" name="action" value="delete" />
    			<?php endif; ?>
    		
    			<?php if ( !$session ) : ?>
    			<button class="btn" type="submit">
    			    <?= @text('LIB-AN-ACTION-ENABLE')?>
    			</button>
    			<?php else : ?>
    			<button class="btn btn-danger" type="submit">
    			    <?= @text('LIB-AN-ACTION-DISABLE')?>
    			</button>
    			<?php endif; ?>
    		</form>
		</div>
	</div>		
<?php endforeach; ?> 
</div>
