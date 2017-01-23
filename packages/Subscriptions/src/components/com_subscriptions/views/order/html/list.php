<? defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
        <h4 class="entity-title">
            <a href="<?= @route('view=order&id='.$order->id) ?>">
                <?= @escape($order->itemName); ?>
            </a>
        </h4>

        <div class="entity-meta">
            <?= $order->createdOn->getDate('%b %d %Y')?>
        </div>

        <div class="entity-description">
            <dl>
                <dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID'); ?></dt>
                <dd><?= $order->itemId ?></dd>

                <dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID'); ?></dt>
                <dd><?= round($order->getTotalAmount(), 2) ?></dd>

                <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD'); ?></dt>
                <dd>
                    <i class="icon-<?= ($order->recurring) ? 'repeat' : 'ok-circle' ?>"></i>
                    <?= ($order->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$order->billingPeriod) ?>
                </dd>

                <dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD'); ?></dt>
                <dd><?= $order->method ?></dd>
            </dl>
        </div>
</div>
