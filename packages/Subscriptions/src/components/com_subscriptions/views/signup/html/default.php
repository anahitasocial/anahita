<? defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step' => 'default')) ?>

<div class="row">
	<div class="span8">

	    <h1>Terms Of Service</h1>

	    <div class="an-entity">
            <h2 class="entity-title">
                <?= @escape($item->name) ?>
            </h2>

            <div class="entity-description">
                <dl>
                    <? if ($item->recurring): ?>
                    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD') ?>:</dt>
                    <dd><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) ?></dd>
                    <? else: ?>
                    <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-DURATION') ?>:</dt>
                    <dd><?= round(AnHelperDate::secondsTo('day', $item->duration)) ?> <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DAYS') ?></dd>
                    <? endif; ?>

                    <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>: </dt>
                    <dd><?= $item->price.' '.get_config_value('subscriptions.currency', 'US') ?></dd>
                </dl>
            </div>
        </div>

        <?

        $tos_layout = get_config_value('subscriptions.tos_content_layout');
        $tos_replace = '<a href="'.@route('option=com_pages&view=content&layout='.$tos_layout).'" target="_blank">'.@text('COM-SUBSCRIPTIONS-TERMS-SERVICE').'</a>';

        $privacy_layout = get_config_value('subscriptions.privacy_content_layout');
        $privacy_replace = '<a href="'.@route('option=com_pages&view=content&layout='.$privacy_layout).'" target="_blank">'.@text('COM-SUBSCRIPTIONS-PRIVACY-POLICY').'</a>';

        $body = sprintf(@text('COM-SUBSCRIPTIONS-TERMS-DESCRIPTION'), $tos_replace, $privacy_replace);
        ?>

        <p class="lead well">
            <?= $body ?>
        </p>

        <p>
            <a href="<?= @route('view=packages') ?>" class="btn btn-large">
                <?=@text('COM-SUBSCRIPTIONS-TERM-CANCEL')?>
            </a>

            <a href="<?=@route('layout=login&id='.$item->id)?>" class="btn btn-primary btn-large">
                <?=@text('COM-SUBSCRIPTIONS-TERM-AGREE')?>
            </a>
        </p>

	</div>
</div>
