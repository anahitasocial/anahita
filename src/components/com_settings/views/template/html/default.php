<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => AnInflector::pluralize($view))) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <div class="an-entity">
          <div class="clearfix">
              <div class="entity-portrait-square">
                  <img src="<?= $item->thumbnail ?>" />
              </div>

              <div class="entity-container">
                  <h4 class="entity-title">
                      <?= @escape($item->name) ?>
                  </h4>
                  <div class="entity-description">
                      <p><?= @escape($item->description) ?></p>
                      <p><i><?= @escape($item->copyright) ?></i></p>
                  </div>

                  <div class="entity-meta">
                      <dl>
                          <dt><?= @text('COM-SETTINGS-TEMPLATES-VERSION') ?></dt>
                          <dd><?= @escape($item->version) ?></dd>

                          <dt><?= @text('COM-SETTINGS-TEMPLATES-LICENSE') ?></dt>
                          <dd><?= @escape($item->license) ?></dd>

                          <dt><?= @text('COM-SETTINGS-TEMPLATES-AUTHOR') ?></dt>
                          <? foreach($item->authors as $author) : ?>
                          <dd>
                              <a href="<?= $author->homepage ?>" target="_blank">
                                <?= @escape($author->name) ?>
                              </a>
                          </dd>
                          <? endforeach; ?>
                      </dl>
                  </div>
              </div>
          </div>

          <div class="entity-description">
            <?= @template('_form') ?>
          </div>
      </div>
  </div>
</div>
