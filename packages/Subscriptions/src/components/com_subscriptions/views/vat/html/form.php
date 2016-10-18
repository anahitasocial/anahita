<? defined('KOOWA') or die('Restricted access'); ?>

<? $vat = empty($vat) ? @service('repos:subscriptions.vat')->getEntity()->reset() : $vat; ?>

<form method="post" action="<?= @route($vat->getURL()) ?>" class="an-entity">

    <fieldset>
        <legend><?= ($vat->persisted()) ? @text('COM-SUBSCRIPTIONS-VAT-EDIT') : @text('COM-SUBSCRIPTIONS-VAT-ADD') ?></legend>

        <div class="control-group">
            <label class="control-label" for="country">
                <?= @text('COM-SUBSCRIPTIONS-VAT-COUNTRY') ?>
            </label>
            <div class="controls">
                <?= @helper('selector.country', array('use_country_code' => true, 'selected' => $vat->country)) ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="taxes">
               <?= @text('COM-SUBSCRIPTIONS-VAT-TAXES') ?>
            </label>

            <? $i = 0; ?>
            <? foreach ($vat->getFederalTaxes() as $tax) : ?>
            <div class="input-append controlls">
                <input class="span2" type="text" name="federal_tax[<?= $i ?>][name]" value="<?= $tax->name ?>" maxlength="20" placeholder="<?= @text('COM-SUBSCRIPTIONS-VAT-TAX-NAME') ?>" />
                <input class="span2" type="number" name="federal_tax[<?= $i ?>][value]" value="<?= $tax->value * 100 ?>" maxlength="20" placeholder="<?= @text('COM-SUBSCRIPTIONS-VAT-TAX-AMOUNT') ?>" />
                <span class="add-on">&#37;</span>
            </div>
            <? $i++ ?>
            <? endforeach; ?>

            <div class="input-append controlls">
                <input class="span2" type="text"  name="federal_tax[<?= $i ?>][name]" maxlength="20" placeholder="<?= @text('COM-SUBSCRIPTIONS-VAT-TAX-NAME') ?>" />
                <input class="span2" type="number" name="federal_tax[<?= $i ?>][value]" maxlength="20" placeholder="<?= @text('COM-SUBSCRIPTIONS-VAT-TAX-AMOUNT') ?>" />
                <span class="add-on">&#37;</span>
            </div>
        </div>

    </fieldset>

    <div class="form-actions">
        <? if ($vat->persisted()): ?>
            <? if (KRequest::type() == 'AJAX'): ?>
            <a data-action="cancel" class="btn" href="<?= @route($vat->getURL().'&layout=list') ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a>
            <? else : ?>
            <a class="btn" href="<?= @route($url) ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a>
            <? endif;?>

            <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
                <?= @text('LIB-AN-ACTION-UPDATE') ?>
            </button>
        <? else : ?>
        <a data-trigger="CancelAdd" class="btn" href="<?= @route($vat->getURL()) ?>">
            <?= @text('LIB-AN-ACTION-CANCEL') ?>
        </a>

        <button data-trigger="Add" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-ADD') ?>
        </button>
        <? endif;?>
    </div>
</form>
