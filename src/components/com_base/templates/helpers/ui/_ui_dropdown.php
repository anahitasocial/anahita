<? if (count($commands) > 0) : ?>
<button class="btn dropdown-toggle" class="dropdown-toggle" data-toggle="dropdown">
	<i class="icon-<?=$icon?>"></i>&nbsp;<span class="caret"></span>
</button>

<ul class="dropdown-menu">
    <? $delete = $commands->extract('delete') ?>
    <? $count = count($commands) ?>
    <? if ($edit = $commands->extract('edit')) : ?>
          <li><?= @helper('ui.command', $edit) ?></li>
    <? endif;?>
    <? if ($subscribe = $commands->extract('subscribe')) : ?>
          <li><?= @helper('ui.command', $subscribe) ?></li>
    <? endif;?>
    <? foreach ($commands as $command) : ?>
          <? $commands->extract($command) ?>
          <li><?= @helper('ui.command', $command) ?></li>
    <? endforeach;?>
    <? if ($delete) : ?>
          <? if ($count) : ?>
          <li class="divider"></li>
          <? endif; ?>
          <li><?= @helper('ui.command', $delete) ?></li>
    <? endif;?>
 </ul>
 <? endif; ?>
