<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
//require('../../css/color-vars.php');
?>
html {overflow-y: auto !important;}
.task-preview table.adminform {width:100%;border-collapse:collapse;}
.task-preview table.adminform td {padding:20px 0 0 0;}
.task-preview .previewFrame {padding:0;width:100%;}

table.adminform th {word-wrap: break-word;}
table.admintable {border-collapse:collapse;border-spacing: 0 !important;}

.mc-thumbnail img {margin: 0 !important;float:left !important; }

.disabled {font-style:normal !important;}
#mc-component .disabled {font-style: italic! important;}

.width-small {width:27%;}
.width-large {width:73%;}

.template-pad {display:none;}

.mc-form-frame div.width-50 {width:40%;}
.mc-form-frame div.width-50.mc-last-column {width:60%;}

.mc-form-frame.gantry-template div.width-50 {width:25%;}
.mc-form-frame.gantry-template div.width-50.mc-last-column {width:75%;}

.gantry-template td.key {display:none;}
.gantry-template .template-title {background:#f6f6f6; color:#000;text-shadow:none;margin:0;padding:4px;font-size: 130%;font-weight:normal;text-spacing:-2px;}

#mc-gantry-header {display:inline-block;width:100%;margin-bottom:25px;}

#mc-gantry-header .mc-value {padding-left:15px;font-size:20px; text-shadow: 1px 1px 0 #fff;}
#params-table #master-bar-desc {font-size: 100%;}

#params-table .update-available #versioncheck-bar {background: #366296;}
#params-table #versioncheck {padding-bottom: 0;}
#params-table #versioncheck-bar {background: #4D4D4D;margin:1px 1px 0;}
#params-table .g-title .download-update span {color:#eee;}

#params-table #diagnostic-bar {background: #81AF22;margin: 1px 1px 0;}
#params-table .errors #diagnostic-bar {background: #B1443C;}

#params-table #master-bar {background: #CD5518; }

.g-inner > table.paramlist.admintable {border:1px solid #f0f0f0;}

#params-table .paramlist_value {font-size: 11px; }

#params-table .g-title {background: #4D4D4D;margin:1px 0 0 0;}
#params-table .paramlist_key {background: #f6f6f6;border-bottom:1px solid #f0f0f0;text-transform:none;}


#params-table .paramlist_key {width: 140px !important; }
#params-table .group-fusionmenu, #params-table .group-splitmenu {margin-left: -166px !important;}