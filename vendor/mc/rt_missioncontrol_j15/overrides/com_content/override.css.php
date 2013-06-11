<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('../../css/color-vars.php');
?>
#mc-article-key .mc-bigger-field {padding-right:150px;}
#mc-article-key .mc-bigger-field input.inputbox {width:450px !important;}

#mc-article-tabs {list-style:none;margin:0 15px;padding:0;overflow:hidden;margin-top:30px;}
#mc-article-tabs li {float:left;margin-right:2px;}
#mc-article-tabs a {display:block;float:left;padding:4px 20px;background:#999;color:#fff;text-decoration:none;cursor:pointer;}
#mc-article-tabs a.active {background:<?php echo $active_bg_color; ?>;color:<?php echo $active_text_color; ?>;}
#mc-article-tabs a:hover, #mc-article-tabs a.active:hover {background:<?php echo $hover_bg_color; ?>;color:<?php echo $hover_text_color; ?>;}

#mc-article {background:#fff;border:1px solid #EDEDED;padding:15px 15px 25px 15px;}

#mc-article .show {display:block;}
#mc-article .hide {display:none;}

#page-editor {margin:-10px;}
#page-editor table.adminform {width:100%;}
#page-editor textarea#text {width:99% !important;}

#page-publishing, #page-advanced {overflow:hidden;}
#mc-pubdata, #mc-metadata, #mc-settings, #mc-statusbox {width: 50%;float:left;}
#mc-pubdata .mc-block, #mc-settings .mc-block {padding-right:20px;}

#mc-statusbox table {display:block;padding:15px;border:1px solid #D9D7AD !important;background:#FFFCD5;margin-bottom:40px !important;width:auto !important;}
#mc-statusbox table td {padding:4px;}
#mc-statusbox .mc-editors-list {border:1px solid #97BDEC;background:#DDE9F7;padding:5px;}
#mc-statusbox .mc-editors-list li {padding:3px;}


.mc-block h3 {line-height:26px;height:26px;font-size:20px;border-bottom:3px solid #E3E3E3;color:#333;font-weight:normal;}

