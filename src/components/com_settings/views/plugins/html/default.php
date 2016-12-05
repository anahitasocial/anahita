<? defined('KOOWA') or die; ?>

<? if ($sort == 'ordering') : ?>
    <? if (defined('ANDEBUG') && ANDEBUG) : ?>
    <script src="com_settings/js/orderable.js" />
    <? else: ?>
    <script src="com_settings/js/min/orderable.min.js" />
    <? endif; ?>
<? endif; ?>

<?
  $url = array();

  if($type){
    $url['type'] = $type;
  }

  if($sort){
    $url['sort'] = $sort;
  }
?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => $view)) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>
      <?= @helper('ui.plugin_types', array(
          'selected' => $type,
          'params' => array(
            'sort' => $sort
          ),
          'id' => 'plugin-type',
          'label' => @text('COM-SETTINGS-PLUGIN-FILTER-TYPE')
      )) ?>

      <table class="table table-hover">
          <thead>
              <th><?= @helper('ui.sorting', array(
                'field' => 'name',
                'url' => $url
              )) ?></th>
              <th>
                <?= @helper('ui.sorting', array(
                  'field' => 'element',
                  'label' => 'COM-SETTINGS-PLUGIN-ELEMENT',
                  'url' => $url
                )) ?>
              </th>
              <th>
                <?= @helper('ui.sorting', array(
                  'field' => 'type',
                  'label' => 'COM-SETTINGS-PLUGIN-TYPE',
                  'url' => $url
              )) ?>
              </th>
              <th>
                <?= @helper('ui.sorting', array(
                  'field' => 'enabled',
                  'label' => 'COM-SETTINGS-PLUGIN-ENABLED',
                  'url' => $url
              )) ?>
              </th>
              <th><?= @helper('ui.sorting', array(
                'field' => 'ordering',
                'url' => $url
              )) ?></th>
          </thead>
          <tbody data-behavior="orderable">
          <?= @template('list') ?>
          </tbody>
      </table>
  </div>
</div>
