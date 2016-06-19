<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => 'assignments')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <p><?= @text('COM-SETTINGS-HEADER-ASSIGNMENTS-DESCRIPTION') ?></p>

      <div class"an-entities">
      <? foreach ($actors as $actor) : ?>
      <div class="an-entity">
          <h3 class="entity-title"><?= ucfirst($actor->package) ?></h3>
          <div class="entity-description">
              <? foreach ($apps as $app) : ?>
              <form action="<?= @route('view=assignment') ?>">
                  <input type="hidden" name="action" value="edit" />
                  <input type="hidden" name="app" value="<?= $app->id ?>" />
                  <input type="hidden" name="actor" value="<?= $actor ?>" />
                  <div class="control-group">
                      <label class="control-label" for="assignable-<?= $app->id ?>">
                        <?= $app->name  ?>
                      </label>

                      <div class="controls">
                          <? $selected = $app->getAssignmentForIdentifier($actor) ?>
                          <select name="access" id="assignable-<?= $app->id ?>" class="input-block-level autosubmit">
                              <? if ($app->getAssignmentOption() == ComComponentsDomainBehaviorAssignable::OPTION_OPTIONAL) : ?>
                              <option <?= ($selected === 0) ? 'selected' : '' ?> value="0">
                                <?= @text('COM-SETTINGS-ASSIGNMENTS-APP-OPTIONAL') ?>
                              </option>
                              <? endif; ?>
                              <option <?= ($selected === 1) ? 'selected' : '' ?> value="1">
                                <?= @text('COM-SETTINGS-ASSIGNMENTS-APP-ALWAYS') ?>
                              </option>
                              <option <?= ($selected === 2) ? 'selected' : '' ?> value="2">
                                <?= @text('COM-SETTINGS-ASSIGNMENTS-APP-NEVER') ?>
                              </option>
                          </select>
                      </div>
                  </div>
              </form>
              <? endforeach; ?>
          </div>
      </div>
      <? endforeach; ?>
      </div>
  </div>
</div>
