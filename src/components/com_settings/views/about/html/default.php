<? defined('KOOWA') or die; ?>

<div class="row">
  <div class="span2">
      <?= @helper('ui.navigation', array('selected' => 'about')) ?>
  </div>
  <div class="span10">
      <?= @helper('ui.header') ?>

      <div class="an-entity">
          <div class="entity-description">
          <p><img src="https://s3.amazonaws.com/anahitapolis.com/media/logos/round_logo.png" alt="Anahita Social Networking Platform and Framework" /></p>
          <h2>Anahita Platform &amp; Framework</h2>
          </div>
          <dl class="entity-meta">
              <dt>Version</dt>
              <dd><?= Anahita::getVersion() ?></dd>
              <dt>License</dt>
              <dd><a href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">GPLv3</a></dd>
              <dt>Website</dt>
              <dd><a href="https://www.getanahita.com" target="_blank">GetAnahita.com</a></dd>
          </dl>
      </div>
  </div>
</div>
