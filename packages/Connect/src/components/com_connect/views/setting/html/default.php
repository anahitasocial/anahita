<? defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-CONNECT-PROFILE-SETTING-TITLE') ?></h3>

<div class="connect-service">
<? foreach ($apis as $api) : ?>

<?
$session = $sessions->find(array('api' => $api->getName()));

if ($session && !$session->validateToken()) {
    $session->delete()->save();
    $session = null;
}
?>
	<div class="an-entity connect-type-<?= $api->getName() ?>">
		<h4 class="entity-title">
			<?= @text(ucfirst($api->getName())) ?>
            <? if (!empty($session)) : ?>
            - <small><?= ($api->getName() == 'twitter') ? '@' : '' ?><?= pick($session->api->getUser()->username, $session->api->getUser()->name) ?></small>
            <? endif; ?>
        </h4>

		<div class="entity-description">
			<?= @text('COM-CONNECT-API-DESC-'.strtoupper($api->getName())) ?>
		</div>

    <?
    if ($session && !$session->validateToken()) {
        $session->delete()->save();
        $session = null;
    }
    ?>
		<div class="entity-actions">
            <?
            $url = array(
                'option' => 'com_connect',
                'view' => 'setting',
                'oid' => $actor->uniqueAlias,
                'server' => $api->getName()
            );
            ?>
    		<form action="<?= @route($url) ?>" method="post">
    			<? if (!$session) : ?>
    			<input type="hidden" name="get" value="accesstoken" />
    			<? else : ?>
    			<input type="hidden" name="action" value="delete" />
    			<? endif; ?>

    			<? if (!$session) : ?>
    			<button class="btn" type="submit">
    			    <?= @text('LIB-AN-ACTION-ENABLE')?>
    			</button>
    			<? else : ?>
    			<button class="btn btn-danger" type="submit">
    			    <?= @text('LIB-AN-ACTION-DISABLE')?>
    			</button>
    			<? endif; ?>
    		</form>
		</div>
	</div>
<? endforeach; ?>
</div>
