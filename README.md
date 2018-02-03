![Anahita social networking platform and framework](https://s3.amazonaws.com/anahitapolis.com/media/logos/homepage_logo.png)

# Anahita

*Version:* 4.3.11 Birth Release

Anahita is a platform and framework for developing open science and knowledge sharing applications on a social networking foundation. Use Anahita to build:

1. online learning and knowledge sharing networks
1. information access networks about people, places, and things
1. open science and open data networks
1. online collaboration environments
1. cloud back-end for your mobile apps

Anahita provides a genuine nodes and graphs architecture as well as design patterns for building social networking apps.

## Concepts

### Nodes
1. **actors:** people, groups, or build your own custom actor
1. **media:** notes, topics, todos, photos, articles, or build your own custom media
1. **hashtags:** all actors, media, and comments are hashtagable  
1. **locations:** all actors and media are geolocatable
1. **stories:** updates created by actors for their followers

### Graphs
1. **social graph:** people and groups can be followed by other people.
1. **hashtags:** for actors, media, and comments
1. **mentions:** tag people in media and comments
1. **locations:** tag locations in media and actors and search nearby nodes  
1. **notifications:** a person receives an email notification whenever a comment is posted on an item they are subscribed to.
1. **votes:** people can Like/Unlike media and comments

### Stories
- story feeds on dashboard and actor profiles
- notifications

### RAD Framework
1. MVC rapid app development framework specialized for building social apps
1. fully customizable theme and user interfaces
1. extendable by social apps and components
1. RESTful and JSON APIs (ideal to use Anahita as a back-end for mobile apps)
1. Built using your favourite technologies such as PHP5, MySql, Bootstrap, JQuery, Grunt, Composer, LessCSS

### Embryo and Birth releases

The code in the master branch is called the **Embryo**. It is what we use to power our website [GetAnahita.com](https://www.getanahita.com) and it is constantly changing and evolving. It may contain bugs that are being fixed. Experimental features may be added and removed. Whenever we reach a specific milestone and the codebase is stable, it is packaged as a **Birth** relase.

## Upgrading

If you are using any previous 4.* versions of Anahita, [here is how to upgrade] (https://www.getanahita.com/articles/158983-updating-from-anahita-4-2-to-4-3)

## Installation

### System Requirements

Before you start please make sure that your server meets the following requirements:

1. Linux or unix server
1. Nginx or Apache 2.0+
1. MySql 5.6+
1. php 5.6+ with OPcache and APCU. Use PHP 7.0+ for best results.
1. Composer package management. You can download it following the instructions on
http://getcomposer.org/ or just run the following command:

`curl -s http://getcomposer.org/installer | php`

#### Important Notes

If you have the suhosin patch installed on your server you might get an error. Add this line to your php.ini file to fix it: `suhosin.executor.include.whitelist = tmpl://, file://`

Anahita is installed and managed via command line interface, because this is the most reliable approach especially after you accumulate large amounts of data in your database.

Also since MySql 5.7 `ONLY_FULL_GROUP_BY` is enabled by default and this is causing errors in Anahita. Connect to your MySql database via command line and run the following command to disable `ONLY_FULL_GROUP_BY`:

### MySql strict mode

Since version 5.7 MySql is set on strict mode by default. That causes Anahita to throw errors, especially if you are
upgrading from older releases. To disable strict mode, you can run the following commands:

`SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));`
`SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''));`

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

Now you need to make some configurations before you can use your Anahita for development or production server. To access the site settings:

1. Login to your Anahita installation as a _Super Administrator_
2. Click on your avatar on the top menubar
3. Click on _Site Settings_. By default you will go to the _Site Settings_

Here are the options on the Site Settings view:

1. **About:** has basic information about your Anahita installation such as creators and software version.
1. **System:** system settings such as site name, database, and mail configurations happen here.
1. **Apps:** configure Anahita apps for the entire site.
1. **Assignments:** configure which actors (People, Groups, etc.) can use what apps (Topics, Photos, etc.)
1. **Plugins:** configure Anahita plugins for the entire site.
1. **Templates:** configure Anahita templates. That is only if they are configurable.

### Notifications

Anahita emails out a lot of email notifications. In order for the notifications to get sent out, you can setup a cron job on your server to the `yourdomain.com/components/com_notifications/process.php` file. Make sure to go to _Site Settings > Apps > Notifications_ and set **Use Cron** to _Yes_.

There are many articles on the web to show you how to setup a cron job. Depending on your number of users and activity on your site, anywhere from 15 minute to 1 hour intervals will work. You will find the suitable interval after monitoring your Anahita installation for a while.  

### Installing Social Apps

![Installing Anahita social apps](http://anahitapolis.com.s3.amazonaws.com/media/gifs/installation/anahita-apps-install.gif)

Now it is time to extend your Anahita installation with some apps and components. Anahita comes with a list of social apps which you can use as they are or use them as blueprints for developing your own custom apps.

To get a list of available apps simply type the following command:

`php anahita package:list`

Now in order to install an app, for example the Photos app, type the following command:

`php anahita package:install photos`

You can even provide a list of apps and components in one line. For example to install the Groups, Topics, and Connect apps use the following command:

`php anahita package:install groups topics connect`

Go to _Site Settings > Assignments_ to define whether an app should optionally or always be available on actor profiles (people, groups, etc.). If an app is optionally available, then on each actor profile the app can be enabled under the _Edit Profile > Apps_.

Congratulations! You have just installed some apps and extensions on your Anahita installation.

### Amazon S3 Storage

Nearly in all cases you wouldn't want to store the uploaded files on your own server. They add up very quickly and that makes it very difficult to maintain or migrate your Anahita installation. Anahita provides a plugin which allows all the uploaded files to be stored in the AWS or [Amazon S3](https://aws.amazon.com/s3/) cloud.

Go to _Site Settings > Plugins_ and then from the _type_ list select _Storage_. Edit and disable the _Storage - Local_ plugin by clicking on it's name. Edit the _Amazon S3_ plugin using the following settings:

1. **Enabled:** set to _yes_
1. **The folder to store the data:** use _assets_ as the default setting
1. **Bucket:** enter the name of your Amazon S3 bucket
1. **Access Key:** enter your AWS access key
1. **Secret Key:** enter your AWS secret key

Now click _Update_ to store the settings. Try uploading your avatar in the front-end and see if it gets uploaded properly. Check the image src to make sure it is an AWS url.

## Join the Anahita Tribe
Anahita has an active and thriving tribe of hackers, entrepreneurs, and hackerpreneurs. They are helpful and friendly. So [Join Us](https://www.GetAnahita.com/join)

**Please Note:** we do not answer questions in email. If you have any questions, please join the Anahita tribe and post your questions on the [Tribe Support](https://www.getanahita.com/groups/107732-tribe-support) group where others can benefit from the answers too.

## Report Bugs or Issues

There are so many ways that you can report us a bug:

- open an issue here on our repository
- start a topic on [Anahita Project](https://www.getanahita.com/groups/42242-anahita-project) group
- send us a [friendly email](https://www.getanahita.com/html/about/contact) and tell us how to reproduce the bug

## Contribute to Anahita

Anahita could never be possible without the help of people in our tribe. We need contributors who can help us with testing, finding and fixing bugs, and coding of course. Here is a [complete guideline](https://www.getanahita.com/articles/162390-contribute-to-anahita) of how you can contribute to Anahita.

## Follow us, Like us

Follow us on twitter [@anahitapolis](https://twitter.com/anahitapolis) and like our facebook page [Facebook.com/anahitasocial](https://www.facebook.com/anahitasocial)

## Credits

Anahita is developed and maintained by [rmdStudio Inc.](https://www.rmdstudio.com) a software development company in Vancouver, Canada. We develop web and mobile apps for scientific, healthcare, and industrial sectors.
