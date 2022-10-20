To view the detailed commits log go to https://github.com/anahitasocial/anahita/commits/master

Anahita 4.6.2
=============================
- fixed: issue with entity not being persisted when behaviours' after.add methods are called. This was resulting into hashtags and mentions not being added when creating an entity.

Anahita 4.6.1
=============================
- added: missing translations for the medium node views article, topic, todo, photo
- fixed: error while following an actor
- changed: made the COM-NOTIFICATIONS-SETTING-URL clear
- added: same_site_none to the global settings
- fixed: delete cover DB record by setting it to empty string instead of null
- fixed: delete portrait DB record by setting it to empty string instead of null
- added: action Read to notifications settings
- fixed: email notification HTML code syntax
- added: getDate to person lastVisitDate
- added: last visit date in person JSON response for the admins to see

Anahita 4.6.0
=============================
- removed: all html views
- removed: swiftmailer
- changed: using symfony/mailer
- changed: updated README file with new documentation that includes configuring an EC2 instance.
- changed: codebase is now compatible with php 7.* and specifically php 7.4.* but not for php 8.* yet

Anahita 4.5.2
=============================
- changed: fileable behaviour now checks for a file containing list of mimetypes otherwise uses an empty array. 
- removed: all the unsupported mimetypes from the com_documents
- changed: improved signup workflow prompt messages
- added: site:signup command to the cli tool 
- removed: first user signup from the people signup controller
- changed: updated installation instructions in the README file.
- added: random_password method to the functions.php
- changed: default trending days for tags to 30 days 

Anahita 4.5.1
=============================
1. changed: privacy update now returns the entity in json response
1. added: access field in json response if viewer has permission to edit the node entity
1. fixed: issue with the missing base_url in the mailer. Now we are obtaining the base_url from com:application.router getBaseUrl
1. removed: template filter aliases from com_people, because it was replicating thee one in the base class.

Anahita 4.5.0
=============================
1. changed: improved RestFUL APIs in authentication, actors, actor settings, media, site settings, stories, comments, and likes.  
1. added: Documents app to the list of packages
1. added: method getFileExtension to the fileable behaviour 
1. added: notifications/clear RESTful endpoint to set all the unsent notifications in the system to sent.
1. added: ability for site admins to set enabled to true or false when editing an actor.
1. changed: only admins and super admins can filter people by usertype, disabled, and email.
1. fixed: several privacy bugs in people, actors, and media browse views
1. changed: increased actor body field to 1000 chars- changed: increased actor body field to 1000 chars
1. added: profile plugin save method is now called when adding an actor.
1. added: enabled field to json object
1. changed: min char requirements to 2 in Person givenName and familyName. Some people have two character names.
1. added: array of administrators to the actor json object
1. removed: administratorIds from the actor json object
1. fixed: a scenario in administrable and followable, which was causing a loop call of removeAdministrator() and removeFollower()
1. added: notes gadgets for dashboard and profiles
1. added: in Actor perrmissionable behaviour added description field to the single permission record.
1. fixed: story is now rendered in the same format provided by the request format.
1. changed: instead of parameter private we are now passing is_private in the post request.
1. added: in Notifications app settings, added an option to mute email notifications.
1. removed: json view of configuration.php file values. Editing this file from a client side application is a bad idea. It's better to edit this file directly on the server.
1. added: server_domain and client_domain to the configuration.php-dist file
1. changed: updated configuration.php-dist file with variables and documentation so people can use it as a reference.
1. changed: improved smtp mail configuration implementation and it is now working with the Mailgun service
1. added: nohtml template
1. changed: config entity and console/config now write well formatted 
variables
1. removed: live_site
1. added: client_domain to the site config
1. fixed: issue which was including lib_anahita js translation file in nohtml template
1. fixed: issue causing add location to crash the app
1. added: CORS settings to the site configs

Anahita 4.4.4
=============================
1. added: search near me functionality to the search component 
1. fixed: issue which was breaking the json search response when viewer was in the results and breaking the list.
1. removed: closest sort from search. It is now happening in combination with recent or most relevant sorting whenever geo search is happening.  
1. added: max length to the alias in describable behaviour
1. changed: tags, locations, and hashtags controllers so they work with the rest api 
1. changed: geolocatable behaviours to work with the REST api. For example addLocation now returns the location object
1. added: JSON view for Browse and Read in site settings component.
1. changed: updated actor settings json response
1. added: actor, optional apps, admins, and extended apps settings to the 
Actor setting json response.
1. added: json views for com_settings views
1. changed: settings mvc to config 
1. changed: all instances com:settings.setting to com:settings.config 
1. changed: improved settings assignments REST API for the Browse and Edit operations
1. added: Actor Apps Settings REST API
1. added: Actor Permissions Settings REST API 
1. added: checkpoints so only the person with the right privileges can view apps, permissions, and privacy values in the REST api.
1. added: permissionable controller behaviour for actors
1. added: permissionable behaviour to the actor controller
1. removed: permissions related methods from administrable behaviour.
1. added: appable controller behaviour
1. removed: app management methods from administrable behaviour
1. changed: moved Privacy settings to its own tab
1. added: privatable controller behaviour
1. removed: old privatable methods from the abstract controller and 
administrable behaviour. 
1. added: actor privacy tab translation labels to the actors translation 
file.
1. added: GetFollowRequests action to the Actor Requestable behaviour 

Anahita 4.4.3
=============================
1. removed: get_magic_quotes_gpc because it has been deprecated
1. changed: in the term filter, added backslash before the dash in 
regular expression.

Anahita 4.4.2
=============================
1. fixed: issue with missing tag name and alias in json response

Anahita 4.4.1
=============================
1. fixed: code for mail message rendering
1. fixed: default person access is public
1. changed: removed isRegistrationOpen in person controller canRead
1. changed: improved signup controller
1. fixed: location entity description which was preventing it to create a new record.
1. fixed: issue with photos multiple uploader which was always setting access value to public
1. fixed: issue with storage folder always being set to assets
1. added: support for AWS S3 regions 
1. changed: updated 3slib.php library
1. changed: using ssl = true at all times with AWS s3
1. fixed: attribute validation in domain entities.
1. fixed: attribute length (min/max) validation.
1. changed: updated attribute validations in all entities.
1. changed: updated form fields to follow the string length limits.
1. changed: applied search term length char limit to 100.
1. changed: in location component we now have 2 API keys. One for the geolocation and other for the map and places. 
1. changed: tags now store inheritance identifiers correctly in the database.
1. changed: renamed hashtagable to hashtaggable everywhere
1. changed: the base class tag node identifier is now included in the type field of hashtags, locations, or any tags extending the tag node.
1. changed: tag node (hashtag, location) json response now contains list of taggables with pagination 
1. added: fake total number for the stories to save on the query operation.
1. added: owner field to the story json object

Anahita 4.4.0
=============================
1. changed: all koowa classes are now merged with Anahita framework. All classes prefixed with K are now using An prefix. Unused code have been removed.  
1. changed: in configuration.php file AnConfig is now AnSiteConfig 
1. changed: coverSet() is now hasCover()
1. changed: portraitSet() is now hasPortrait()
1. added: cover to node list, actor list, and person list layouts
1. changed: using + for all new (entity) buttons

Anahita 4.3.14 Birth Release
=============================
1. fixed: plyr styling 
1. changed: KException to AnException 

Anahita 4.3.13 Birth Release
=============================
1. fixed warnings in php 7.2.8
1. disables ONLY_FULL_GROUP_BY per db session if it is enabled. This mode is enabled by default in MySql 5.7 which was preventing Anahita to work properly.
1. moved KDatabase to AnDatabase
1. removed legacy KDatabase classes

Anahita 4.3.12 Birth Release
=============================
1. fixes in com_people and com_actors REST APIs
1. fixes in template overrides mechanism
1. locations services now only requires one API key from google maps platform 
1. fixed facebook and twitter OAuth.
1. facebook OAuth is now readonly, because facebook as deprecated publish_stream and publish_actions permission.
1. twitter OAuth is now available for people actors only for the sake of simplicity. Database migration will remove the paired group actors with twitter.
1. Nooku/Koowa KHelper, KMixin, KCommand, KEvent, KRequest, and KHttp moved to Anahita library as AnHelper, AnMixin, AnCommand, AnEvent, AnRequest, and AnHttp.
1. added cover image to articles 
1. added notifications to articles 
1. removed actor avatar and cover stories
1. updated Shiraz styling to accommodate recent changes
1. removed parallax jquery plugin  


Anahita 4.3.11 Birth Release
=============================
1. removed assignable behavior from component entity
1. users are now permanently logged in until they logout
1. merged data, inflector, behaviors, view, and template from Nooku into Anahita and removed unused code.

Anahita 4.3.10 Birth Release
=============================
1. updated plyr.io javascript library to version 2.0.18
1. fixed authentication with email bug
1. fixed session add bug for json requests
1. fixed the 401 error page which was trying to load itself
1. site:symlink command now works for both windows and nix platforms

Anahita 4.3.9 Birth Release
=============================
1. fixed bug on the login form
1. fixed bug on person profile redirects after each save
1. updated README.md to include instructions on disabling MySql strict mode
1. improved login session creation code

Anahita 4.3.8 Birth Release
=============================
1. fixed bugs that prevented people to stay logged in if they chose to
1. KController is AnController now
1. refactored and merged Dispatcher controllers
1. removed legacy files that were no longer in use.
1. improved RESTful authentication and login  
1. added blockquote tag support to the editor

Anahita 4.3.7 Birth Release
=============================
1. added Google reCaptcha plugin
1. fixed issue which was preventing all system plugins to load on pages other than Dashboard
1. a number of bug fixes and code improvements

Anahita 4.3.6 Birth Release
=============================
1. Subscriptions App: fixed profile setting view
1. Subscriptions App: fixed orders view for non admins
1. Subscriptions App: highlight disabled packages
1. Subscriptions App: show prompt message that says no packages are available

Anahita 4.3.5 Birth Release
=============================
1. freshly grunted js files added

Anahita 4.3.4 Birth Release
=============================
1. asking for pronouns instead of gender on person profile edit
1. if custom language package doesn't exist, default to en-GB
1. if custom template package doesn't exist, default to shiraz
1. delete language package directory when language package is uninstalled
1. added OpenGraph meta tags to media and actor node detailed views
1. fixed issue in the language class which was preventing the use of custom packages
1. moved photo set cover to the main column
1. fixed issue which was breaking inline photo title/description editing
1. used rel=nofollow for the voters action link
1. clean up meta description text
1. fixed issue where the mentions RegEx was parsing @ symbols within urls
1. refined email RegEx
1. cleaned up and updated schemas
1. fixed WSOD when users deleted their own accounts
1. fixed issue where people with disabled accounts could login and see a broken page
1. improved speed by 25% by some code optimization
1. fixed issue which prevented errors to be displayed within the custom template    
1. updated minimum php version of all packages to php >= 5.6
1. display the last 20 notifications in the notifications popup
1. fixed issue where plugins were being imported multiple times
1. fixed issue preventing commenting on articles
1. added before/after events for person save and delete in user plugins
1. updated Connect App code for facebook
1. added parallax effect to actor profile covers

Anahita 4.3.3 Birth Release
=============================
* fixed a number of issues in the notifications component
* used infinite scroll for the list of Transactions in the Subscriptions app
* fixed bug that prevented editing mentionable nodes
* added actor bar for the notes list view
* minimum php requirement is now set to version 5.6

Anahita 4.3.2 Birth Release
=============================
* fixed critical bug in the migration query bug which was adding all nodes to the people_people table.

Anahita 4.3.1 Birth Release
=============================
* fixed issue which was causing a WSOD whenever a guest viewed an actor profile accessible to registered people only.

Anahita 4.3.0 Birth Release
=============================
* removed administration back-end
* implemented com_settings for super admins to configure Anahita
* removed all joomla legacy files and replaced them with native anahita classes
* removed all xml files and started using json files instead
* node description and excerpt fields now support utf8mb4 for displaying emojis
* updated robots.txt file
* added emoji support to node names, aliases, and hashtags

Anahita 4.2.4 Birth Release
=============================
* fixed critical issue which was preventing first time installation due to the absence of a from system email

Anahita 4.2.3 Birth Release
=============================
* starting using SwiftMail and Anahita's Mailer class instead of legacy joomla mailer
* improved the code that sends out notification mails
* updated email regex according to the latest w3c specs
* added tags url attributes for people, actors, and masonry photos so they can be filtered by hashtag, mention, or location
* updated robots.txt file

Anahita 4.2.2 Birth Release
=============================
* fixed issue preventing hashtag term showing as title in the hashtag view
* displays userType to viewer in json
* gadget must be given a url before loading
* urls in shiraz example pages fixed

Anahita 4.2.1 Birth Release
=============================
* fixed issue which was preventing authentication with email instead of username
* fixed issue which was including search and composer js files twice
* feature: site admins can now manually add new locations to be used by the community
* feature: recently added tags can now be shown for hashtags and locations
* refactored the code in com_tags and com_medium components

Anahita 4.2.0 Birth Release
=============================
* Tag actors and media with locations
* Capture browser's geolocation data and store them in media nodes
* List top and trending locations
* Search nearby actors and media nodes
* Integration with google maps and location api to render maps, geolocate, etc.
* com_pages is now com_articles therefore we call it the Articles app.
* com_html is now com_pages
* Improved com_tags implementation
* Updated Shiraz template to contain com_pages layouts
* Fixed bugs which allowed creation of duplicate edges
* Improved the implementation of javascript InfiniteScrolls and Masonry layout rendering
* Ability for site admins to mark actor profiles as verified

Anahita 4.1.8 Birth Release
=============================
* fixed issue with the db table coalitions problem. Now all the tables are set to utf-8 and Engine=InnoDB
* fixed issues in the Invites app tokens and UIs

Anahita 4.1.7 Birth Release
=============================
* fixed the issue which was breaking the actor social graph pagination
* fixed the issue which was preventing notification settings to be stored in default layout
* changed the label follow/unfollow to get/stop notifications for medium node Subscriptions
* medium nodes are enabled by default

Anahita 4.1.6 Birth Release
=============================
* fixed bug which was preventing new users to update their password after token login
* fixed remember me bug

Anahita 4.1.5 Birth Release
=============================
* people management is now happening all in the front-end
* removed legacy user manager from the back-end
* removed legacy joomla ACL
* implemented simple and natively supported ACL and 4 user types: public, registered, administrator, super administrator
* updated database schema and dropped legacy tables
* we now have nodes and edges tables without the anahita prefix
* refactored com_people MVC. We are still relying on JUser, but we are closer to a fully native user manager
* Only admins and super admins can enable and disable accounts.
* refactored the user activation and password reset workflows
* update the migration files and fixed the issues that was causing the older releases of Anahita to break during the migration process.
* updated the Subscriptions app to work with the new person entities.
* beautified the code using php-cs-fixer

Anahita 4.1.4 Birth Release
=============================
* fixed validation of whether an edge had the same node at both ends
* migration script to remove all the edges in the database which had the same node at both ends
* UI refinements
* migration script to change todos_todos table to InnoDB
* added an editable placeholder for the photos which had no title or description

Anahita 4.1.3 Birth Release
=============================
* major updates to the Subscriptions app. We are now using it ourselves.
* fixed editable bug in Photos app set title and description
* removed access plugin from Subscriptions app
* deleted more Joomla legacy files such as com_cache
* added Pinnable behaviour so the Pages and Topics can use it
* fixed gist JQuery plugin which couldn't parse multiple urls
* fixed favicon overwrite bug


Anahita 4.1.2 Birth Release
=============================
* Upgraded facebook OAth API
* Upgraded linkedIn OAuth API
* fixed inline edit and cancel issue where nested an-entity layers were being created
* major upgrade of the Subscriptions App. No it isn't ready yet. Wait for the next release.
* discontinued OpenSocial plugin
* apps can now be specific to what type of actors they can be assigned to. Subscriptions app can only be assigned to person actors

Anahita 4.1.1 Birth Release
=============================
* ability to run the entire site with SSL on
* fixed infinit scroll bugs in social graph and other entities
* fixed notification scroll bug
* fixed permalinks in comment stories and notifications
* fixed the default list limit bug in the admin back-end
* lots of development on the Subscriptions app, but sorry it isn't ready for this release yet.
* removed legacy force_ssl and implemented a global isSSL() auto detection method

Anahita 4.1.0 Birth Release
=============================
* all the mootools code has been removed
* the entire javascript library has been rewritten in JQuery
* added covers for the actor profiles and coverable behavior to the librariy which can be used for other types of nodes such as locations.
* improvements to the social graph API
* removed TinyMCE and instead developed a new lightweight html5 editor that is being used in Topics and Pages apps
* drag'n drop multiple file uploader in the Photos app
* improved usability of the user interfaces for mobile users
* added gist content plugin for sharing code snips
* simplified and improved the social graph API
* removed ptag content filter
* all comments do not support html content. They do however use content filters
* users can no longer add a and img tags in posts.
* added grunt.js file for compressing the js files

Anahita 4.0.4 Birth Release
============================
* removed routing from component configuration form in the admin back-end. The routing was preventing the form to save on some Nginx servers.

Anahita 4.0.3 Birth Release
============================
* minor but annoying mistake fixed. The error page wasn't loading the correct analytics template causing the error page to break.

Anahita 4.0.2 Birth Release
============================
* removed all JRoute::_ instances for the urls in the admin backend. We did that becuase some Nginx servers had problem routing the urls properly and were landing on 404 pages instead.
* analytics code is now a layout template inside the templates/base/html/tmpl and it can be customized within your tempalte.
* minor UI fixes

Anahita 4.0.1 Birth Release
============================
* version number is updated
* the broken layout of Page read view is fixed
* added permission method to make sure that only those with the edit access can set the privacy of a medium node.

Anahita 4.0.0 Birth Release
============================
* #hashtags implemented
* @mentions implemented
* com_tags implemented as the base component for different types of tagging
* Social graph in com_people refactored
* group admins or followers can now add additional followers to the group
* legacy com_menus has been removed
* com_search layout improved. Results are now loaded as infinit scroll
* menus are now hardcoded layouts in templates/base/html/menus
* viewer menu is now generated dynamically
* legacy com_modules has been removed
* removed all module positions from the Shiraz template
* no more support for <module> tags in the layouts. We are using generic Bootstrap grids from now on
* removed milestones MVC from the Todos app
* refinements to the wysiwyg editor
* improvements to the Pages app
* general performance improvements
* a lot of legacy Joomla files and components are removed

Anahita 3.0.4
==============

* ed4f06f video links with both http and https are now being parsed
* ffa0586 A valid username starts with a letter and it may contain numbers

Anahita 3.0.3
==============

* d0c8fed added the publish template method back so admins can publish their own custom templates.
* 6bf4f87 Strict standard warning fixed.
* 9e92ddf removed the editor until we completely remove this legacy component in the next release.
* b17490f by default set the assignment to always
* 8cdf6f7 Strict standard warning fixed
* c9c4a71 Update avatar_edit.php
* 3935031 adds site:symlink command to console
* d013438 commented out line for @flash_message added back

Anahita 3.0.2
==============

* cd92071 - added authentication to the auto login just in case if a bad cookie was handed in
* 902ad4d - Legacy joomla cleanup
* 197cf57 - Uses authentication and the remember me has been re-implemented
* feb2e93 - added login and logout functions
* edf907f - legacy joomla classes removed
* 733fc1f - The generated session for remember me was too long. It has been shortened quite a bit.
* 9e2e139 - Updated homepage description
* 78de1c2 - Added the fix suggested by @kulbakin that removes the index.php from the urls even when logging in and out.
* 53981e6 - improved the search query
* f90451c - the text highlight now has an additional variable to set the minimum length of a string that needs to be highlighted. By the default the value is 3 or more
* ac3a9cb - new migration makes the fields `email` and `username` unique in the #__users table.
* df44482 - fixed strict standard warnings
* 78095cc - entity title now wraps properly
* 61a9f3e - Fixed the issue with excessive br tags in the visual editor. It is a hack until we replace the editor with something better. Such as Markdown.
* 6e3ee50 - Made the description a bit more clear
* 270e89f - prompt messages show after the successful submission
* 3e97928 - alert colour is changed to success from the default blue.
* dbe8c9e - if response is not null and if it is successful
* 10af5a5 - invitation send prompt shows after a successful response
* f08b2e5 - Improved the authentication workflow in the Connect app. Although there is still room for improvement.
* 1710918 - fixed the bug in LinkedIn share feature
* 0a1febd - verifiable ssl must be off by default
* 9d9975f - all video links must use https.
* d1f85ea - added the AnFilterTerm class
* 8af510b - Fixed strict standard warning
* e9d949b - q request is now being sanitized
* 8feae12 - Fixed strict warning
* 48598cc - Search box utilizes the custom filter in com_search now
* 8bf2279 - added custom filters to session and search controllers
* f3ce28a - Fixed strict warning
* 807a091 - Added custom filters for search terms and session return values
* 8fdcd10 - new site.js has been compressed
* ad94d19 - Legacy and discontinued Anahita apps are removed.
* 1773531 - Fixed more strict standard warnings and errors
* c1379e9 - sanitize the session return value to prevent xss injection
* b787a11 - Fixing a whole bunch of strict standard warnings and errors
* b31cd83 - sessions wasn't being assigned to the view. Also fixed the invites.
* 0015caa - Added new methods to get facebook app id and friends
* 4690e60 - added data-request-redirect="true"
* e23a5f4 - There was an unnecessary repeated line of code. It i s removed.
* 6bec455 - Removed the scroll attribute
* fb94f27 - Action is now being called correctly
* e2192f1 - using secure connections to connect to twitter
* 315dbff - using secure connections to the services
* 0075750 - consider_html=true added to the truncate call
* 717d11f - The notification button no longer shows if the viewer isn't following a profile
* 41cf798 - removed legacy parameters
* 47f3f94 - User types are no longer hard coded to Registered upon registration and follow the setting in the global config file.
* 88fe2a0 - Fixed the permissions for actor creation as well as the available options for group creation permissions.
* 50f84da - statusUpdateTime misspelling is corrected
* 9c0040d - Fixed the bug that showed a 404 page to the blocked user rather than a permission denied message.
* 4097441 - Fixed the spelling of "matched"
* 7f98819 - added the item layout
* 26c3d64 - Checks to see if the response body is set, then concatenates it.
* 6c2a17d - Not sure why that variable was being used. Removed it.
* c7d3838 - legacy feature. Removed logo upload feature.
* b8b96ce - A list of strict standard issues are resolved. Had to modify the ordering methods to become more consistent with the Nooku method calls. The com_components lis
* 43630d4 - A list of strict standard warning issues are resolved. Mainly static method calls.
* bf7999b - Subtracted the Strict standard warnings from all the warnings.

Anahita 3.0.0
==============
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
strategy you need to rewrite this migration and add your own policy
* Removed Joomla Installer
* Fixed a installation issue in the data.sql. Renamed all instances of com_posts in the 41 component record
to com_notes. Didn't write a migration for it since it was alreay in the migration 1  
