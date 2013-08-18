* Removed the com_content, com_section, com_categories. If you have existing aricles you can migrate them into
html and use it within the com_html. Read https://github.com/anahitasocial/article-exporter/blob/master/README.md
After updating you need to resymlink the site using the command `php anahita site:init -n`
* Removed  back-end components: com_checking,com_admin and unused libraires (SimplePIE, DOMit, Geshi). Editors 
plugins, search and content plugins
If you are using these libriares in your apps then use the composer to install them  
* Moved the HTML component to the core that way the basic installation anahita can use the HTML component
for building landing pages. After updating you need to resymlink the site using the command `php anahita site:init -n`
* Prevent having dot in username. A migration has been added to remove all the dots in the username. A username
can only contain ^[A-Za-z0-9][A-Za-z0-9_-]*$. The migration will replace all the . with the work dot. If you want a different
strategy you need to rewrite this migration and add your own policy - Arash Sanieyan
* Removed Joomla Installer - Arash Sanieyan 
* Fixed a installation issue in the data.sql. Renamed all instances of com_posts in the 41 component record
to com_notes. Didn't write a migration for it since it was alreay in the migration 1  