<?php defined('KOOWA') or die('Restricted access'); ?>

<h3><?= @text('COM-SUBSCRIPTIONS-PROFILE-INFORMATION') ?></h3>
        
<?php if($viewer->admin()): ?>        
<?= @template('_edit') ?>        
<?php elseif(!$actor->hasSubscription()): ?>
<div class="alert alert-warning">
    <p><?= @text('COM-SUBSCRIPTIONS-SUBSCRIPTIONS-NOT-SUBSCRIBED') ?></p>
    <p>
        <a href="<?= @route('view=packages') ?>" class="btn">
            <?= @text('COM-SUBSCRIPTIONS-SUBSCRIPTION-ACTION-SIGN-ME-UP') ?>
        </a>
    </p>
</div>

<?php elseif( $subscription->expired() ): ?>
<div class="alert alert-error">
    <p><?= @text('COM-SUBSCRIPTIONS-PACKAGE-HAS-EXPIRED') ?></p>
    
    <p>
        <a href="<?= @route('view=packages') ?>" class="btn btn-warning">
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE-RENEW') ?>
        </a>
    </p>
</div>
<?php else: ?>

<?php $package = $subscription->package; ?>

<div class="an-entity">
    <h2 class="entity-title">
        <?= @escape($package->name) ?>
    </h2>
    
    <div class="entity-description">
        <dl>
            <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD') ?></dt>
            <dd><?= ($package->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$package->billingPeriod) ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-DURATION') ?>:</dt>
            <dd><?= round(AnHelperDate::secondsTo('day', $package->duration)) ?> <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DAYS') ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>:</dt> 
            <dd><?= $package->price.' '.get_config_value('subscriptions.currency','US') ?></dd>
        </dl>
    </div>
    
    <div class="entity-description">
        <?= @content( $package->description ) ?>
    
        <?php if ( !$package->recurring && $package->authorize('upgradepackage') ) : ?>
        <p>
            <a href="<?=@route(array('view'=>'signup','id'=>$package->id))?>" class="btn">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-UPGRADE-NOW') ?>
            </a>
        </p>
        <?php elseif ( $package->authorize('subscribepackage') ) : ?>
        <p>
            <a href="<?=@route(array('view'=>'signup','id'=>$package->id))?>" class="btn">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE-NOW') ?>
            </a>
        </p>    
        <?php endif; ?>
    </div>
</div>

<?php if( !$package->recurring ): ?>
<?php $daysLeft = ceil( AnHelperDate::secondsTo('day', $subscription->getTimeLeft())); ?>
<div class="alert alert-<?= ($daysLeft < 31) ? 'warning' : 'info' ?>">
    <p><?= sprintf(@text('COM-SUBSCRIPTIONS-PACKAGE-ABOUT-TO-EXPIRE'), $daysLeft); ?></p>
    
    <?php if( $daysLeft < 31 ) : ?>
    <p>
        <a href="<?= @route('view=packages') ?>" class="btn btn-warning">
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE-RENEW') ?>
        </a>
    </p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>