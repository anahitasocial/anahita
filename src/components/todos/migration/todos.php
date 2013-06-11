<?php 

function todos_1()
{
    dbexec('ALTER TABLE #__todos_todos CHANGE open_status_change_by open_status_change_by BIGINT( 11 ) NULL');
    dbexec('ALTER TABLE #__todos_milestones CHANGE todolists_count todolists_count INT( 11 ) NULL');
    
    dbexec("UPDATE #__anahita_nodes SET `name` = 'todo_disable' WHERE `component` LIKE 'com_todos' AND `name` LIKE 'todo_closed'");
    dbexec("UPDATE #__anahita_nodes SET `name` = 'todo_enable'  WHERE `component` LIKE 'com_todos' AND `name` LIKE 'todo_opened'");
    
    dbexec("UPDATE #__anahita_nodes SET `name` = 'todo_add'  WHERE `component` LIKE 'com_todos' AND `name` LIKE 'new_todo'");
    dbexec("UPDATE #__anahita_nodes SET `name` = 'todolist_add'  WHERE `component` LIKE 'com_todos' AND `name` LIKE 'new_todolist'");
    dbexec("UPDATE #__anahita_nodes SET `name` = 'milestone_add'  WHERE `component` LIKE 'com_todos' AND `name` LIKE 'new_milestone'");
    dbexec("DELETE FROM #__anahita_nodes WHERE `name` IN ('new_todolist','new_todo','new_milestone','todo_closed','todo_opened')");    
}
