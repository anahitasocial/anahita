<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
    <h3 class="entity-title">
        <?= @text(LibBaseTemplateHelperSelector::$COUNTRIES[ $vat->country ] ) ?>
    </h3>
    
    <div class="entity-description">
        <dl>
            <dt></dt>
            <dd></dd>
        </dl>
    </div>
    
    <div class="entity-meta">
        <ul class="an-meta">
            <?php if(isset($vat->author)) : ?>
            <li><?= sprintf( @text('LIB-AN-ENTITY-AUTHOR'), @date($vat->creationTime), @name($vat->author)) ?></li>
            <?php endif; ?>
            
            <?php if(isset($vat->editor)) : ?>
            <li><?= sprintf( @text('LIB-AN-ENTITY-EDITOR'), @date($vat->updateTime), @name($vat->editor)) ?></li>
            <?php endif; ?>       
        </ul>
    </div>
    
    <div class="entity-actions">
        <?= @helper('ui.commands', @commands('list')) ?>
    </div>
</div>