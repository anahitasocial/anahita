<?php defined('KOOWA') or die; ?>

<?php @service('application.dispatcher')->getRequest()->tmpl = 'component' ?>
<?php $user = @service('repos://site/users')->getQuery(true)->fetchValue('id'); ?>

<div class="row">
	<div class="offset3 span6">	
        
        <?php if(!$user): ?>
        <div class="alert alert-info alert-block">
            <h4><?= @text('COM-PEOPLE-WELCOME1') ?></h4>
            <p><?= @text('COM-PEOPLE-WELCOME2') ?></p>
        </div>
        <?php endif; ?>	
        
		<?= @template('form') ?>
	</div>
</div>
