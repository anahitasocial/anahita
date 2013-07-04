<?php defined('KOOWA') or die('Restricted access') ?>
<?php 
$btn     = <<<EOF
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
$well    = <<<EOF
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
		      <td style="padding:20px 10px 20px 10px;background-color:#ffffff">
                <table cellspacing="0" cellpadding="0" border="0" style="">
                     <tbody>
                      <tr><?= $output ?></tr>
                      </tbody>
                </table>
		      </td>
		   </tr>
		</tbody>
	</table>
</td>
</tr>
</tbody>
</table>

