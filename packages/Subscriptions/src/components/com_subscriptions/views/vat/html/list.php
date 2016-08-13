<? defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
    <h3 class="entity-title">
        <?= @text(LibBaseTemplateHelperSelector::$COUNTRIES[ $vat->country ]) ?>
    </h3>

    <div class="entity-description">
        <dl>
            <? foreach ($vat->getFederalTaxes() as $tax) :?>
            <dt><?= @text('COM-SUBSCRIPTIONS-VAT-TAXES') ?></dt>
            <dd><?= $tax->name ?></dd>
            <dd><?= $tax->value ?> &#37;</dd>
            <? endforeach; ?>
        </dl>
    </div>

    <ul class="an-meta">
        <? if (isset($vat->author)) : ?>
        <li><?= sprintf(@text('LIB-AN-ENTITY-AUTHOR'), @date($vat->creationTime), @name($vat->author)) ?></li>
        <? endif; ?>

        <? if (isset($vat->editor)) : ?>
        <li><?= sprintf(@text('LIB-AN-ENTITY-EDITOR'), @date($vat->updateTime), @name($vat->editor)) ?></li>
        <? endif; ?>
    </ul>

    <div class="entity-actions">
        <?= @helper('ui.commands', @commands('list')) ?>
    </div>
</div>
