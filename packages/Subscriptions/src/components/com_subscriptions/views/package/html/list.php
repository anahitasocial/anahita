<? defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity <?= ($package->enabled) ? '' : 'an-highlight' ?>">
    <h3 class="entity-title">
        <? if ($package->authorize('administration')): ?>
        <a href="<?= @route($package->getURL()) ?>">
            <?= @escape($package->name); ?>
        </a>
        <? else: ?>
        <?= @escape($package->name); ?>
        <? endif; ?>
    </h3>

    <div class="entity-description">
        <dl>
            <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD') ?></dt>
            <dd><?= ($package->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$package->billingPeriod) ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-DURATION') ?>:</dt>
            <dd><?= round(AnHelperDate::secondsTo('day', $package->duration)) ?> <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DAYS') ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>:</dt>
            <dd><?= $package->price.' '.get_config_value('subscriptions.currency', 'US') ?></dd>
        </dl>
    </div>

    <div class="entity-description">
        <?= @content($package->description) ?>
    </div>

    <? if (!$package->authorize('administration')): ?>
        <? if ($package->authorize('upgradepackage')) : ?>
        <div class=entity-actions>
            <a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-block btn-warning">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-UPGRADE-NOW') ?>
            </a>
        </div>
        <? elseif ($package->authorize('subscribepackage')) : ?>
        <div class="entity-actions">
            <a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-success btn-block">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE-NOW') ?>
            </a>
        </div>
        <? endif; ?>
    <? endif; ?>

    <? if ($package->authorize('administration')): ?>
    <div class="entity-actions">
        <?= @helper('ui.commands', @commands('list')) ?>
    </div>
    <? endif; ?>
</div>
