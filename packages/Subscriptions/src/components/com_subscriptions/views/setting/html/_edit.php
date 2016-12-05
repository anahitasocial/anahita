<? defined('KOOWA') or die('Restricted access'); ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_subscriptions/js/setting.js" />
<? else: ?>
<script src="com_subscriptions/js/min/setting.min.js" />
<? endif; ?>

<? $action = ($selectedPackageId) ? 'editsubscription' : 'addsubscriber'; ?>

<form action="<?= @route('view=package') ?>" method="post" />
    <input type="hidden" name="action" value="<?= $action ?>" />
    <input type="hidden" name="actor_id" value="<?= $actor->id ?>" />

    <div class="control-group">
        <label for="package">
            <?= @text('COM-SUBSCRIPTIONS-PACAKGE') ?>
        </label>

        <div class="controls">
        <? foreach ($packages as $package): ?>
            <label class="radio">
                <? $checked = ($package->id == $selectedPackageId) ? 'checked' : ''; ?>
                <input required type="radio" name="package_id" value="<?= $package->id ?>" <?= $checked ?> />
                <?= @escape($package->title) ?>
            </label>
        <? endforeach; ?>
        </div>
    </div>

    <? if (count($packages)): ?>
    <div class="control-group">
        <label class="class-label">
            <?= @text('COM-SUBSCRIPTIONS-SUBSCRIPTION-EXPIRY-DATE') ?>
        </label>

        <div class="controls">
            <?= @helper('selector.day', array('name' => 'day', 'required' => '', 'selected' => $endDate->day, 'class' => 'span2')) ?>
            <?= @helper('selector.month', array('name' => 'month', 'required' => '', 'selected' => $endDate->month, 'class' => 'span2')) ?>
            <?= @helper('selector.year', array('name' => 'year', 'required' => '', 'selected' => $endDate->year, 'class' => 'span2')) ?>
        </div>
    </div>
    <? endif; ?>

    <div class="form-actions">
        <? if ($subscription) : ?>
        <a data-trigger="DeleteSubscriber" class="btn btn-danger">
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-UNSUBSCRIBE') ?>
        </a>
        <? endif; ?>

        <button type="submit" class="btn btn-primary">
            <? if ($subscription) : ?>
            <?= @text('LIB-AN-ACTION-UPDATE') ?>
            <? else : ?>
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE') ?>
            <? endif; ?>
        </button>
    </div>
</form>
