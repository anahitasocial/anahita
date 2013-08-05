<?php defined('KOOWA') or die ?>

<?php @title(sprintf(@text('COM-NOTES-META-TITLE-NOTE'), $actor->name).' - '.@date($note->creationTime)) ?>
<?php @description(@helper('text.truncate', strip_tags($note->body), array('length'=>156))) ?>


<?php @commands('toolbar') ?>

<module position="sidebar-b" style="none"></module>

<?= @template('note') ?>

<?= @helper('ui.comments', $note) ?>