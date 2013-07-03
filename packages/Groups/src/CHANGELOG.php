<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
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
! -> Note?>

===Version 1.1.1===
# Fixed ordering of group lists to DESC
^ gadgets now use @name instead of @actor_name
^ gadgets now use @truncate rather than the string helper truncate
# oid=viewer is now working

===Version 1.1===
# Fixed invalid variable bug in the group/form.php 
# Fixed Groups that a person Administering filter bug
^ views/groups/tmpl/administrating.* has been changed to views/groups/tmpl/administering.*
+ Default groups layout now displays the list of first 20 groups sorted by lastUpdateTime
+ Search groups layout has been added
+ Added basic HTML support for the description field

===Version 1.0.2===
^ views/group/tmpl/form.php code has been cleaned up
# The untranslated bread crumb item in Group Administering view is now translated
^ the component modules are now assiend for sidebar-b instead of sidebar-a

===Version 1.0.1===
^ Leader's Following actor header link is removed if the actor and viewer aren't the same
+ leaders, following, and administrating layouts have been added for the groups
^ group controller has been modified so it can handle the new layouts.
^ default groups layout now displays the search and create new group link
- search layout removed from the groups view 
# breadcrumbs have been fixed for the birth release

