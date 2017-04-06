<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => 'templates')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <div class="an-entities">
          <? foreach($items as $item): ?>
          <div class="an-entity <?= ($item->isDefault()) ? 'an-highlight' : ''; ?>">
              <div class="clearfix">
                  <div class="entity-portrait-square">
                      <a href="<?= @route('view=template&alias='.$item->alias) ?>">
                          <img src="<?= $item->thumbnail ?>" alt="<?= $item->name ?>" />
                      </a>
                  </div>

                  <div class="entity-container">
                      <h4 class="entity-title">
                        <a href="<?= @route('view=template&alias='.$item->alias) ?>">
                          <?= $item->name ?>
                        </a>
                      </h4>

                      <div class="entity-description">
                      <?= @escape($item->description) ?>
                      </div>

                      <div class="entity-meta">
                          <ul class="an-meta inline">
                              <li><?= @text('COM-SETTINGS-TEMPLATES-VERSION') ?>: <?= $item->version ?></li>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
          <? endforeach; ?>
      </div>
  </div>
</div>
