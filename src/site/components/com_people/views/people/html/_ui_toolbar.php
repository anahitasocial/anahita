<?php defined('KOOWA') or die ?>

<?php if ($viewer->admin()) : ?>
<div class="btn-toolbar">
    <a href="<?= @route('view=person&layout=add') ?>" class="btn btn-primary">
        <?= @text('COM-PEOPLE-TOOLBAR-PERSON-NEW') ?>
    </a>
</div>
<?php endif; ?>

<form action="<?= @route('layout=list') ?>" id="an-filterbox" class="an-filterbox form-inline" name="an-filterbox" method="get">
    <input placeholder="Filter..." type="text" name="q" class="input-large search-query" id="an-search-query" value="" size="21" maxlength="100" />
    <?php if ($viewer->admin()) : ?>
    <?php
    $usertypes = array(
        '' => JText::_('COM-PEOPLE-FILTER-USERTYPE'),
        ComPeopleDomainEntityPerson::USERTYPE_REGISTERED => @text('COM-PEOPLE-USERTYPE-REGISTERED'),
        ComPeopleDomainEntityPerson::USERTYPE_ADMINISTRATOR => @text('COM-PEOPLE-USERTYPE-ADMINISTRATOR'),
        ComPeopleDomainEntityPerson::USERTYPE_SUPER_ADMINISTRATOR => @text('COM-PEOPLE-USERTYPE-SUPER-ADMINISTRATOR'),
    );
    $html = $this->getService('com:base.template.helper.html');
    ?>
    <?= $html->select('filter[usertype]', array('options' => $usertypes)) ?>
    <label class="checkbox">
        <input type="checkbox" name="filter[disabled]">
        <?= @text('COM-PEOPLE-FILTER-DISABLED') ?>
    </label>
    <?php endif; ?>
</form>
