<? defined('KOOWA') or die('Restricted access'); ?>

<? $highlight = ($coupon->expired()) ? '' : 'an-highlight' ?>

<div class="an-entity <?= $highlight ?>">
    <h3 class="entity-title">
        <?= $coupon->code ?>
    </h3>

    <div class="entity-description">
        <dl>
            <dt><?= @text('COM-SUBSCRIPTIONS-COUPONE-DISCOUNT') ?></dt>
            <dd><?= $coupon->discount * 100 ?> &#37;</dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-COUPONE-EXPIRES-ON') ?></dt>
            <dd><?= @date($coupon->expiresOn) ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-COUPONE-LIMIT') ?></dt>
            <dd><?= $coupon->limit ?></dd>

            <dt><?= @text('COM-SUBSCRIPTIONS-COUPONE-USAGE') ?></dt>
            <dd><?= $coupon->usage ?></dd>
        </dl>
    </div>

    <div class="entity-meta">
        <ul class="an-meta">
            <? if (isset($coupon->author)) : ?>
            <li><?= sprintf(@text('LIB-AN-ENTITY-AUTHOR'), @date($coupon->creationTime), @name($coupon->author)) ?></li>
            <? endif; ?>

            <? if (isset($coupon->editor)) : ?>
            <li><?= sprintf(@text('LIB-AN-ENTITY-EDITOR'), @date($coupon->updateTime), @name($coupon->editor)) ?></li>
            <? endif; ?>
        </ul>
    </div>

    <div class="entity-actions">
        <?= @helper('ui.commands', @commands('list')) ?>
    </div>
</div>
