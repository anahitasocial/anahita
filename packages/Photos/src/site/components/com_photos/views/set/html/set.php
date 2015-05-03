<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<div class="an-entity an-photos-set editable" data-url="<?= @route($set->getURL()) ?>">             
    <div class="entity-description-wrapper">
        <h3 class="entity-title">
            <?= @escape( $set->title ) ?>
        </h3>
        
        <div class="entity-description">
            <?= @content( nl2br( $set->description ), array( 'exclude' => array('gist','video') ) ) ?>
        </div>
    </div>
    
    <div class="entity-meta">
        <div class="an-meta" id="vote-count-wrapper-<?= $set->id ?>">
        <?= @helper('ui.voters', $set); ?>
        </div>
    </div>
</div>

<?php if( $set->authorize('edit') ) : ?>
<div id="photo-selector"></div>
<?php endif; ?>


<div id="set-photos" class="an-entities" data-url="<?= @route($set->getURL()) ?>">
	<div class="media-grid">
    <?= @template('photos') ?>
    </div>
</div>