<?php if ( count($commands) > 0 ) : ?>
<button class="btn dropdown-toggle"><i class="icon-<?=$icon?>"></i>&nbsp;<span class="caret"></span></button>
<ul class="dropdown-menu">
    <?php $delete = $commands->extract('delete') ?> 
    <?php $count  = count($commands) ?>
    <?php if ( $edit = $commands->extract('edit')) : ?>
          <li><?= @helper('ui.command', $edit) ?></li>                  
    <?php endif;?>
    <?php if ( $subscribe = $commands->extract('subscribe')) : ?>
          <li><?= @helper('ui.command', $subscribe) ?></li>        
    <?php endif;?>        
    <?php foreach($commands as $command) : ?>
          <?php $commands->extract($command) ?>
          <li><?= @helper('ui.command', $command) ?></li>
    <?php endforeach;?>    
    <?php if ( $delete ) : ?>
          <?php if ( $count ) : ?>
          <li class="divider"></li>
          <?php endif; ?>
          <li><?= @helper('ui.command', $delete) ?></li>
    <?php endif;?>
 </ul>
 <?php endif; ?>