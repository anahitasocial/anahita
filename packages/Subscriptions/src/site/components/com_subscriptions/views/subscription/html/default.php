<?php defined('KOOWA') or die('Restricted access'); ?>

<module position="sidebar-b" style="none"></module>

<?php if(!$actor->hasSubscription()): ?>
<div class="alert alert-warning">
	<p><?= @text('COM-SUB-SUBSCRIPTIONS-NOT-SUBSCRIBED') ?></p>
	<p>
		<a href="<?= @route('view=packages') ?>" class="btn">
			<?= @text('COM-SUB-SUBSCRIPTION-ACTION-SIGN-ME-UP') ?>
		</a>
	</p>
</div>

<?php elseif($subscription->expired()): ?>
<div class="alert alert-error">
	<p><?= @text('COM-SUB-PACKAGE-HAS-EXPIRED') ?></p>
	
	<p>
		<a href="<?= @route('view=packages') ?>" class="btn btn-warning">
			<?= @text('COM-SUB-PACKAGE-ACTION-SUBSCRIBE-RENEW') ?>
		</a>
	</p>
</div>
<?php else: ?>
<?php $package = $subscription->package; ?>
<div id="sub-package">
	<h3 class="package-title"><?= @escape($package->name) ?></h3>
	
	<div class="package-info">
		<div class="key"><?= @text('COM-SUB-BILLING-PERIOD') ?>:</div> 
		<div class="value"><?= ($package->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$package->billingPeriod) ?></div>
	</div>
	
	<div class="package-info">
		<div class="key"><?= @text('COM-SUB-PACKAGE-PRICE') ?>:</div> 
		<div class="value"><?= $package->price.' '.get_config_value('subscriptions.currency','US') ?></div>
	</div>
	
	<div class="package-description">
		<?= @content( $package->description ) ?>
	
		<?php if ( !$package->recurring && $package->authorize('upgradepackage') ) : ?>
		<p>
			<a href="<?=@route(array('view'=>'signup','id'=>$package->id))?>" class="btn">
				<?= @text('COM-SUB-PACKAGE-ACTION-UPGRADE-NOW') ?>
			</a>
		</p>
		<?php elseif ( $package->authorize('subscribepackage') ) : ?>
		<p>
			<a href="<?=@route(array('view'=>'signup','id'=>$package->id))?>" class="btn">
				<?= @text('COM-SUB-PACKAGE-ACTION-SUBSCRIBE-NOW') ?>
			</a>
		</p>	
		<?php endif; ?>
	</div>
	
	<?php if(!$package->recurring): ?>
	<?php $daysLeft = ceil( AnHelperDate::secondsTo('day', $subscription->getTimeLeft())); ?>
	<div class="alert alert-<?= ($daysLeft < 31) ? 'warning' : 'success' ?>">
		<p><?= sprintf(@text('COM-SUB-PACKAGE-ABOUT-TO-EXPIRE'), $daysLeft); ?></p>
		
		<?php if( $daysLeft < 31 ) : ?>
		<p>
			<a href="<?= @route('view=packages') ?>" class="btn btn-warning">
				<?= @text('COM-SUB-PACKAGE-ACTION-SUBSCRIBE-RENEW') ?>
			</a>
		</p>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>