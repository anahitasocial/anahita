<? defined('KOOWA') or die('Restricted access') ?>

<? if (count($composers)) : ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_composer/js/_composer.js" />
<script src="com_locations/js/geoposition.js" />
<? else: ?>
<script src="com_composer/js/min/_composer.min.js" />
<script src="com_locations/js/min/geoposition.min.js" />
<? endif; ?>

<div id="com-composer-container" data-behavior="Composer">
    <div class="clearfix">
        <div class="btn-group pull-right">
            <? $array = array_values($composers->getObjects()); ?>
            <button class="btn dropdown-toggle" class="dropdown-toggle" data-toggle="dropdown">
            	<i class="icon-plus-sign"></i>
            	<span class="composer-button-title"><?=$array[0]->title?></span>
            	<span class="caret"></span>
            </button>

            <ul id="composer-menu" class="dropdown-menu">
            <? foreach ($composers as $composer) : ?>
                <li>
                	<a href="#" title="<?= $composer->title ?>">
                	<?= $composer->title ?>
                	</a>
                </li>
            <? endforeach;?>
            </ul>
        </div>
    </div>
    <div class="tab-content">
    <? foreach ($composers as $index => $composer) : ?>
        <div class="tab-content-item" data-url="<?=@route($composer->url) ?>">
            <a class="form-placeholder"><?= $composer->placeholder ?></a>
        </div>
    <? endforeach;?>
    </div>
</div>
<? endif;?>
