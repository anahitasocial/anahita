<? defined('KOOWA') or die ?>

<? $trendingHashtags = $gadgets->extract('hashtags-trending'); ?>
<? $trendingLocations = $gadgets->extract('locations-trending'); ?>

<div class="row">

    <div class="span2">
        <? if (count($gadgets) >= 1): ?>
        <ul class="nav nav-pills nav-stacked streams">
            <li class="nav-header">
            <?=  @text('LIB-AN-STREAMS') ?>
            </li>
            <? foreach ($gadgets as $index => $gadget) : ?>
            <li data-stream="<?= $index ?>" class="<?= ($index == 'stories') ? 'active' : ''; ?>">
            	<a href="#<?= $index ?>" data-toggle="tab"><?= $gadget->title ?></a>
            </li>
            <? endforeach;?>
        </ul>
        <? endif; ?>
    </div>

    <div class="span6" id="container-main">

        <?= @helper('com:composer.template.helper.ui.composers', $composers) ?>

        <div class="tab-content">
            <? foreach ($gadgets as $index => $gadget) : ?>
            <div class="tab-pane fade <?= ($index == 'stories') ? 'active in' : ''; ?>" id="<?= $index ?>">
            	<?= @helper('ui.gadget', $gadget) ?>
            </div>
            <? endforeach;?>
        </div>
    </div>

    <div class="span4 visible-desktop">
        <?= @helper('ui.gadget', $trendingHashtags) ?>
        <?= @helper('ui.gadget', $trendingLocations) ?>
    </div>
</div>
