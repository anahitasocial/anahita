![Anahita social networking platform and framework](https://s3.amazonaws.com/anahitapolis.com/media/logos/homepage_logo.png)

# Anahita

*Version:* 4.1.2 Birth Release

Anahita is a social networking platform for building knowledge sharing apps and services. Use Anahita to launch:

1. online learning and knowledge sharing networks
2. information access networks about people, places, and things
3. open science and open data networks
4. online collaboration environments
5. cloud back-end for your mobile apps

Anahita provides a genuine nodes and graphs architecture as well as design patterns for building apps that are smarter and inherently social.

## Concepts

### Nodes
1. **actors:** people, groups, or build your own custom actor
2. **media:** notes, topics, todos, photos, pages, or build your own custom media
3. **hashtags:** all actors, media, and comments are hashtagable  
4. **stories:** updates created by actors for their followers

### Graphs
1. **social graph:** people and groups can be followed by other people. 
2. **hashtags:** for actors, media, and comments
3. **mentions:** tag people in media and comments
4. **notifications:** a person receives an email notification whenever a comment is posted on an item they are subscribed to.
5. **votes:** people can Like/Unlike media and comments

### Stories
- story feeds on dashboard and actor profiles
- notifications

### RAD Framework
1. MVC rapid app development framework specialized for building social apps 
2. fully customizable theme and user interfaces
3. extendable by social apps and components
4. RESTful and JSON APIs (ideal to use Anahita as a back-end for mobile apps)
5. Built using your favourite technologies such as PHP5, MySql, Bootstrap, JQuery, Grunt, Composer, LessCSS

### Embryo and Birth releases

The code in the master branch is called the **Embryo**. It is what we use to power our website [GetAnahita.com](http://www.getanahita.com) and it is constantly changing and evolving. It may contain bugs that are being fixed. Experimental features may be added and removed. Whenever we reach a specific milestone and the codebase is stable, it is packaged as a **Birth** relase.

## Installation

### System Requirements

Before you start please make sure that your server meets the following requirements:

1. Linux or unix server
2. Apache 2.0+ (with mod_mysql, mod_xml, mod_zlib) or Nginx
3. MySql 5.0+
4. php 5.3.3+ with APC
5. Composer package management. You can download it following the instructions on
http://getcomposer.org/ or just run the following command:

`curl -s http://getcomposer.org/installer | php`

#### Important Notes

1. If you have the suhosin patch installed on your server you might get an error. Add this line to your php.ini file to fix it: `suhosin.executor.include.whitelist = tmpl://, file://`
2. If you have Zend Optimizer on your server *disable it*!
3. Anahita is installed and managed via command line interface, because this is the most reliable approach especially after you accumulate large amounts of data in your database.

### Installing a stable package 

![Installing Anahita using the Birth release code](http://anahitapolis.com.s3.amazonaws.com/media/gifs/installation/anahita-installation-birth.gif)

Stable packages are called _Birth_ releases. Use the following command to create an Anahita project called _myproject_. This command automatically downloads all the required files from the [Anahita GitHub repository](https://github.com/anahitasocial):

`composer create-project anahita/project myproject`

Now go to the _myproject_ directory:

`cd myproject`

Continue with [Initiating Installation] (#initiating-installation) from this point.

### Installing from the master branch

![Installing Anahita using the Embryo release code](http://anahitapolis.com.s3.amazonaws.com/media/gifs/installation/anahita-installation-embryo.gif)

The master branch always contains the _Embryo_ release. Using the following command, clone the Anahita repository from the master branch:

`git clone git@github.com:anahitasocial/anahita.git myproject`

change directory to *myproject*

`cd myproject`

Now run the composer command to obtain all the 3rd party libraries that Anahita requires:

`composer update`

Continue with *Initiating Installation* from this point.

## Initiating Installation

![Initiating Anahita installation](http://anahitapolis.com.s3.amazonaws.com/media/gifs/installation/anahita-installation-init.gif)

If you type _php anahita_ you get a list of all commands available to manage your Anahita installation. If the command didn't work, perhaps the symlink to the anahita command line tool isn't created. In this case run the following command to create a symlink. Otherwise move to the next step which is initiating the installation process.

`ln -s bin/anahita anahita`

In order to initiate the installation process run the following command and provide your database information when it is asked from you:

`php anahita site:init`

The Anahita installation is created in the _PATH-TO-YOUR-DIRECTORY/myproject/www_ directory. You need to configure your server to use this directory as the public directory. 

The first account that is created on this installation becomes the _Super Administrator_ account. Go to the _http://www.YOUR-DOMAIN-NAME.com/people/signup_ and create an account.

Congratulations! You have installed Anahita successfully. Now you need to configure your installation and install some apps.

## Configuring Your Anahita Installation

Now you need to make some configurations before you can use your Anahita for development or production server. Go to _/administrator_ to access the administration back-end. 

**Please Note:** the administration back-end will be removed in Anahita 4.3 and all the administration features will be available on the front to the users with _admin_ and _super admin_ privileges.

### Global Configuration

In the admin back-end go to the _Configure_ tab. Here are the main settings that you should care about:

**Site Name:** the name of your site. This will show up in all the notifications emailed out to people.

**User Settings:** 

- **Allow User Registration:** normally it should be _yes_ unless you are using Anahita for a small team and adding   the members to your network one by one or adding members automatically using an Anahita app such as the Subscriptions app.

- **New User Registration Type:** we recommend _registered_ as the default
  
- **New User Account Activation:** If set to _yes_ people who sign up receive a link which they have to click on in order to activate their account. 

**Route Setting:** Set to _yes_ to get nice urls. If you are on an Apache server, you need to rename the _htaccess.txt_ file in the _www_ directory to _.htaccess_ for this feature to work.

**Server Settings:** Set _Error Reporting_ to _none_ on production server.

**Debug Settings:** Set _Debug System_ to _yes_ if you are developing on Anahita or debugging an app.

**Cache Setting:** You don't need to use caching when developing, but on your production server select _APC_ from the list and set Cache to _yes_. This will significantly speed up your site.

**Session Settings:** We prefer _Database_ as the default setting.

**Mail Settings:** We prefer _PHP Mail Function_ as the default setting. Enter a valid email address for _Mail From_ this will show up in all the email notifications that people receive from Anahita. A valid email address will reduce the possibility of them getting picked up by spam filters. Enter your server's _SMTP_ settings in the appropriate fields so notification emails get sent out. 

Don't forget to _Save_ or _Apply_ or settings. Your settings are saved in the _www/configuration.php_ file so make sure that this file has the appropriate write permissions.

### Notifications

Anahita emails out a lot of email notifications. In order for the notifications to get sent out, you need to setup a cron job on your server. In the admin back-end under the _Extend_ tab go to the _Notifications_ component. There you find the path and url that you need to use in your cron. There are many articles on the web to show you how to setup a cron job. Depending on your number of users and activity on your site, anywhere from 15 minute to 1 hour intervals will work. You will find the suitable interval after monitoring your Anahita installation for a while.  

### Installing Social Apps

![Installing Anahita social apps](http://anahitapolis.com.s3.amazonaws.com/media/gifs/installation/anahita-apps-install.gif)

Now it is time to extend your Anahita installation with some apps and components. Anahita comes with a list of social apps which you can use as they are or use them as blueprints for developing your own custom apps.

**Please Note:** the _Subscriptions_ app has not yet been upgraded for the Anahita 4.1. The updated version should be available by June 2015.

To get a list of available apps simply type the following command:

`php anahita package:list`

Now in order to install an app, for example the Photos app, type the following command:

`php anahita package:install photos`

You can even provide a list of apps and components in one line. For example to install the Groups, Topics, and Connect apps use the following command:

`php anahita package:install groups topics connect`

In the administration back-end you can go to the _Extend > Components_ and further define whether an app should optionally or always be available on actor profiles (people, groups, etc.). If an app is optionally available, then on each actor profile the app can be enabled under the _Edit Profile > Apps_.

Congratulations! You have just installed some apps and extensions on your Anahita installation.

### Amazon S3 Storage

Nearly in all cases you wouldn't want to store the uploaded files on your own server. They add up very quickly and that makes it very difficult to maintain or migrate your Anahita installation. Anahita provides a plugin which allows all the uploaded files to be stored in the AWS or [Amazon S3](https://aws.amazon.com/s3/) cloud. 

Under the _Extend_ tab go to the _Plugin Manager_ and then from the _type_ list select _Storage_. Disable the _Storage - Local_ plugin by clicking on the checkmark under the Published column. Then click on the _Storage - Amazon S3_ to edit the plugin. Configure the plugin with the following setting:

- **The folder to store the data:** use _assets_ as the default setting
- **Bucket:** enter the name of your Amazon S3 bucket
- **Access Key:** enter your AWS access key
- **Secret Key:** enter your AWS secret key
- **Published:** set to _yes_

Now click save to store the settings. Try uploading your avatar in the front-end and see if it gets uploaded properly. Check the image src to make sure it is an AWS url.

## Join the Anahita Tribe
Anahita has an active and thriving tribe of hackers, entrepreneurs, and hackerpreneurs. They are helpful and friendly. So [Join Us](http://www.GetAnahita.com/join) 

**Please Note:** we do not answer questions in email. If you have any questions, please join the Anahita tribe and post your questions on the [Tribe Support](http://www.getanahita.com/groups/107732-tribe-support) group where others can benefit from the answers too.

## Report Bugs or Issues

There are so many ways that you can report us a bug:

- open an issue here on our repository
- start a topic on [Anahita Project](http://www.getanahita.com/groups/42242-anahita-project) group
- send us a [friendly email](http://www.getanahita.com/html/about/contact) and tell us how to reproduce the bug 

## Contribute to Anahita

Anahita could never be possible without the help of people in our tribe. We need contributors who can help us with testing, finding and fixing bugs, and coding of course. Here is a [complete guideline](http://www.getanahita.com/html/tribes/contribute) of how you can contribute to Anahita.

## Follow us, Like us

Follow us on twitter [@anahitapolis](https://twitter.com/anahitapolis) and like our facebook page [Facebook.com/anahitasocial](https://www.facebook.com/anahitasocial)

## Credits

Anahita is developed and maintained by [rmdStudio Inc.](http://www.rmdstudio.com) a Vancouver software development company specialized in knowledge sharing cloud and mobile apps. 
