<?php  if (!empty ($module->content)) : ?>
    <?php if ($params->get('moduleclass_sfx')!='') : ?>
        <div class="<?php echo $params->get('moduleclass_sfx'); ?>">
    <?php endif; ?>
    <div class="block">
    	<?php if ($module->showtitle != 0) : ?>
        <h2 class="module-title"><?php echo $module->title; ?></h2>
        <?php endif; ?>
        <div class="module-content">
        <?php echo $module->content; ?>
        </div>
    </div>
    <?php if ($params->get('moduleclass_sfx')!='') : ?>
        </div>
    <?php endif; ?>
<?php endif; ?>