You can use the style 3 to create your own custom theme. 
For any of the *less files in the ROOT/templates/base/css 
directory you can create overwrite less files in this directory. 
For example:

style3/apps/stories.less
style3/bootstrap/variables.less
style3/colors/colors.less
style3/core/actor.less
style3/images/spinner.gif

The go to the administration back-end > global configuration > system settings
switch Debug System and refresh one of the pages in the front end. 
That will cause the less compiler that comes with Anahita to create a new style3/style.css file.
Then switch off the debug system in the back-end once you are done customizing the shiraz theme.