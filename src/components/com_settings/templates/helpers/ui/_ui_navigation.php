<? defined('KOOWA') or die; ?>

<ul id="setting-tabs" class="nav nav-pills nav-stacked" >
    <li class="nav-header">
        <?= @text('COM-SETTINGS-NAV-HEADER') ?>
    </li>
    <? foreach ($tabs as $i=>$tab) :?>
    <li <?= ($i == $selected) ? 'class="active"' : '' ?>>
        <a href="<?= @route($tab['url']) ?>">
          <?= @text($tab['label']) ?>
        </a>
    </li>
    <? endforeach; ?>
</ul>
