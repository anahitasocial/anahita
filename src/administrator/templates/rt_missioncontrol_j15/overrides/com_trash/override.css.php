<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('../../css/color-vars.php');
?>
body.option-com-trash td.mc-last-cell div {background: none;border:0 !important;}
body.option-com-trash td.mc-last-cell a {padding:15px;text-decoration:none !important;font-weight:bold;text-align:center;border:0 !important;}
body.option-com-trash td.mc-last-cell img {display:none;}
body.option-com-trash a.icon-32-delete {padding:15px !important;margin:20px !important;}
body.option-com-installer.task-installer table.adminform td,
body.option-com-installer.task- table.adminform td {padding:10px 0 35px;}
body.option-com-trash td.mc-last-cell a {background:#666;color:#fff;}
body.option-com-trash td.mc-last-cell a:hover {background:<?php echo $hover_bg_color;?>;}