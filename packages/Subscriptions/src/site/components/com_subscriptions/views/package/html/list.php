<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
    <h3 class="entity-title">
        <?php if( $package->authorize('administration') ): ?>
        <a href="<?= @route( $package->getURL() ) ?>">
            <?= @escape( $package->name ); ?>
        </a>
        <?php else: ?>
        <?= @escape( $package->name ); ?>    
        <?php endif; ?>    
    </h3>   

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
        <?= $package->description ?>
    </div>
    
    <?php if( !$package->authorize('administration') ): ?>
        <?php if ( $package->authorize('upgradepackage') ) : ?>
        <div class=entity-actions>
            <a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-block btn-warning">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-UPGRADE-NOW') ?>
            </a>
        </div>
        <?php elseif ( $package->authorize('subscribepackage') ) : ?>
        <div class="entity-actions">
            <a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-block">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE-NOW') ?>
            </a>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if( $package->authorize('administration') ): ?>
    <div class="entity-actions">
        <?= @helper('ui.commands', @commands('list')) ?>
    </div>
    <?php endif; ?>
</div>