You can use the style 2 to create your own custom theme. 
For any of the *less files in the ROOT/templates/base/css 
directory you can create overwrite less files in this directory. 
For example:

style2/core/template.less
style2/images/logo.png

Then go to the administration back-end > site templates > your template > parameters then
- set "Style" = "Style 2",
- set "Compile Style" = "Only if changed"
and refresh one of the pages in the front end.
That will cause the less compiler that comes with Anahita to create a new style2/style.css file.
Then switch off the debug system in the back-end once you are done customizing the shiraz theme.