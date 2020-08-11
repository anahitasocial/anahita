<? defined('ANAHITA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-APPS') ?></h3>

<div class="an-entities">
	<? foreach ($apps as $app) : ?>
	<div class="an-entity">
		<h4 class="entity-title">
			<?= $app->getProfileName() ?>
		</h4>

		<div class="entity-description">
		    <?= $app->getProfileDescription() ?>
		</div>

		<div class="entity-actions">
    		<? if (!$app->enabledForActor($item)) : ?>
    		<a  class="btn btn-primary" data-action="addapp" data-app="<?= $app->component ?>" href="<?= @route($item->getURL() . '&format=json') ?>">
    			<?= @text('COM-ACTORS-APP-ACTION-INSTALL') ?>
    		</a>
    		<? else : ?>
    		<a class="btn" data-action="removeapp" data-app="<?= $app->component ?>" href="<?= @route($item->getURL() . '&format=json') ?>">
    			<?= @text('COM-ACTORS-APP-ACTION-UNINSTALL') ?>
    		</a>
    		<? endif;?>
		</div>
	</div>
	<? endforeach;?>
</div>
