<?php if ( count($composers) ) : ?>

<script src="com_composer/js/composer.js" />
<div id="com-composer-container">   
    <div class="clearfix" data-behavior="ComposerTabs">
        <div class="btn-group pull-right">
            <?php 
                $array = array_values($composers->getObjects());
            ?>
            <button class="btn dropdown-toggle">
            	<i class="icon-plus-sign"></i>
            	<span class="composer-button-title"><?=$array[0]->title?></span>
            	<span class="caret"></span>
            </button>
            
            <ul class="dropdown-menu">  
            <?php foreach($composers as $composer) : ?>
                <li><a href="#"><?= $composer->title ?></a></li>
            <?php endforeach;?>
            </ul>    
        </div>
    </div>
    <div class="tab-content">   
    <?php $i = 0; ?>
    <?php foreach($composers as $index=>$composer) : ?>
        <div data-behavior="PlaceHolder"  data-placeholder-element=".form-placeholder" data-placeholder-area="!#com-composer-container" data-trigger="LoadComposerTab" data-loadcomposertab-index="<?= $i++ ?>" data-content-url="<?=@route($composer->url)?>">
            <div class="form-placeholder"><span><?= $composer->placeholder ?></span></div>
        </div>
    <?php endforeach;?>
    </div>
</div>
<?php endif;?>