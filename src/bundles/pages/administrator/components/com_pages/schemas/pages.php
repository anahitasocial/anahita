<?php 

function pages_1()
{    
    dbexec("UPDATE jos_anahita_nodes SET `name` = 'page_enable'  WHERE `component` LIKE 'com_pages' AND `name` LIKE 'page_publish'");
    dbexec("DELETE FROM jos_anahita_nodes WHERE `component` LIKE 'com_pages' AND `name` LIKE 'page_edit'");
}

?>