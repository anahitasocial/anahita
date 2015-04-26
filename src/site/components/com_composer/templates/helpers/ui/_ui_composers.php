<?php if ( count($composers) ) : ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_composer/js/composer.js" />
<?php else: ?>
<script src="com_composer/js/min/composer.min.js" />
<?php endif; ?>

<div id="com-composer-container" data-behavior="Composer">   
    <div class="clearfix">
        <div class="btn-group pull-right">
            <?php $array = array_values($composers->getObjects()); ?>
            <button class="btn dropdown-toggle" class="dropdown-toggle" data-toggle="dropdown">
            	<i class="icon-plus-sign"></i>
            	<span class="composer-button-title"><?=$array[0]->title?></span>
            	<span class="caret"></span>
            </button>
            
            <ul id="composer-menu" class="dropdown-menu">  
            <?php foreach($composers as $composer) : ?>
                <li>
                	<a href="#" title="<?= $composer->title ?>">
                	<?= $composer->title ?>
                	</a>
                </li>
            <?php endforeach;?>
            </ul>    
        </div>
    </div>
    <div class="tab-content">   
    <?php foreach($composers as $index=>$composer) : ?>
        <div class="tab-content-item" data-url="<?=@route($composer->url) ?>">
            <a class="form-placeholder"><?= $composer->placeholder ?></a>
        </div>
    <?php endforeach;?>
    </div>
</div>
<?php endif;?>