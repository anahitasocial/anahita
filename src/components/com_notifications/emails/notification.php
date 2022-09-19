<?php defined('ANAHITA') or die('Restricted access') ?>
<?
$btn = <<<EOF
  text-decoration:none;
  cursor:pointer;
  display:inline-block;
  background-color:#e6e6e6;
  background-repeat:no-repeat;
  padding:5px 10px 5px;
  color:#333333;
  font-size:13px;
  line-height:normal;
  border:1px solid #cccccc;
  border-bottom-color:#bbbbbb;
EOF;
$btn_primary = $btn.<<<EOF
  color:#ffffff;
  background-color:#076da0;
  border-color:#076da0 #076da0 #043b57;
EOF;
$btn_danger = $btn.<<<EOF
  color:#ffffff;
  background-color:#c43c35;
  border-color:#c43c35 #c43c35 #882a25;
EOF;
$well = <<<EOF
  background-color:#f2f2f2;
  margin-bottom:20px;
  padding:10px;
  min-height:20px;
  border:1px solid #eeeeee;
EOF;
?>
<table width="98%" cellspacing="0" cellpadding="40" border="0">
  <tbody>
    <tr>
      <td width="100%" bgcolor="#f7f7f7" style="font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;">
        <table width="620" cellspacing="0" cellpadding="0" border="0">
          <tbody>
            <tr>
              <td style="<?= $well?>"><?= @text('Hi').' '.$person->name?></td>
            </tr>
            <tr>
              <td style="padding:20px 10px 20px 10px;background-color:#ffffff">
                <table cellspacing="0" cellpadding="0" border="0" style="">
                  <tbody>
                    <tr>
                      <? if ($subject) : ?>
                      <td valign="top">
                        <a href="<?= @route($subject->getURL())?>">
                          <img src="<?= @helper('com:actors.template.helper.getAvatarURL', $subject) ?>" />
                        </a>
                      </td>
                      <? endif;?>
                      <td valign="top" style="padding-left:10px">
                        <? if ($title) : ?>
                        <div style="font-size:16px">
                          <?= $title ?>
                        </div>
                        <? endif;?>
                        <? if ($body) : ?>
                        <div style="padding-top:10px;font-size:12px">
                          <?= trim($body) ?>
                        </div>
                        <?php endif; ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <? if (!empty($commands)) : ?>
            <? $setting_command = $commands->extract('notification_setting'); ?>
            <tr>
              <td style="<?= $well ?>">
                <? foreach ($commands as $command) : ?>
                  <a href="<?= @route($command->attribs->href) ?>" style="<?= $btn_primary  ?>">
                    <?= $command->label?>
                  </a>
                <? endforeach; ?>
              </td>
            </tr>
            <?php endif;?>
            <tr>
              <td>
                <small>
                <? if (!empty($setting_command)) : ?>
                  <?= sprintf(@text('COM-NOTIFICATIONS-SETTING-URL'),
                    @route($setting_command->actor->getURL() . "/notifications"),
                    @route($setting_command->actor->getURL()),
                    $setting_command->actor->name) ?>
                <? endif;?>
                </small>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
