<?php 
// no direct access
defined( 'KOOWA' ) or die( 'Restricted access' );
?>
1. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and the LICENSE file.

2. Changelog
------------
This is a non-exhaustive (but still near complete) changelog

Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

===Version 1.2.2=== 
# updated the board model and controller to handle the new changes of how the Published field is handled in the media node.
^ the topic comment form textarea styling now has the same styling as the topic body form

===Version 1.2.1 ===
^ markup language instructions have been updated.
+ socialplugin has been added to the topic read view

===Version 1.2===
- css color codes have been removed from the discussions.css and added to the template styles 

=== version 1.1 ===
^ views/topics/tmpl/search.php code has been cleaned up
^ views/topics/tmpl/search_leaders.php code has been cleaned up
^ the component modules are now assiend for sidebar-b instead of sidebar-a
# Fixed a typo in the topic model

=== version 1.0.2 ===
- leaders controller and views have been removed
^ topics MVC has been refactored. New layouts for leaders and leaders seach have been added
^ comments controller and views have been modified to support leaders comments search
^ application.php has been updated to accomodate the changes to the leaders view refactor
+ last commenter information has been added to the dashboard gadget
^ last commenter information in the topic layout has been improved
^ tag COM-DISCUSSIONS-TOPIC-LAST-COMMENT-BY in the translation file now exclude the datetime
- media/js/admin.js has been removed
- media/js/topic.js has been removed
# search.js nolonger throws error when radio buttons are clicked before the form is submitted 
# actor header links now work properly