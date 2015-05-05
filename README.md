![Anahita social networking platform and framework](https://s3.amazonaws.com/anahitapolis.com/media/logos/homepage_logo.png)

# Anahita

*Version:* 4.1.0 Embryo Release

Anahita is a remarkable social networking platform for developing knowledge sharing apps and services. Use Anahita to launch:

1. online learning and knowledge sharing networks
2. information access networks about people, places, and things
3. open science and open data networks
4. online collaboration environments
5. cloud back-end for your mobile apps

Anahita provides a genuine nodes and graphs architecture as well as design patterns for building apps that are smarter and inherently social.

## Features

### Nodes
1. *actors:* people, groups, or build your own custom actor
2. *media:* notes, topics, todos, photos, pages, or build your own custom media
3. *hashtags:* all actors, media, and comments are hashtagable  
4. *stories:* updates created by actors for their followers

### Graphs
1. *social graph:* people and groups can be followed by other people. 
2. *hashtags:* for actors, media, and comments
3. *mentions:* tag people in media and comments
4. *notifications:* a person recieves an email notification whenever a comment is posted on an item they are subscribed to.
5. *votes:* people can Like/Unlike media and comments

### Stories
- story feeds on dashboard and actor profiles
- notifications

### More Features
1. *media composer:* for posting notes, topics, pages, todos, and photos from actor profiles or the dashboard.
2. *commnets:* all media are commentable 
3. *privacy management:* for actors and media nodes
4. fully customizable theme and user interfaces
5. extendable by social apps and components
6. specialized Anahita framework to build your own custom social apps
7. RESTful and JSON APIs (ideal to use Anahita as a back-end for mobile apps)

### Built using your favourite technologies
PHP, MySql, Bootstrap, JQuery, Grunt, Composer, LessCSS

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
3. Anahita is installed and managed via commandline, becuase this is the most reliable approach especially after you accumulate large amounts of data in your database.

### Using the Birth Release code

A **Birth** release is a stable release of Anahita. Use the following command to create an Anahita project called _myproject_. This command automatically downloads all the required files from the [Anahita GitHub repository](https://github.com/anahitasocial):

`composer create-project anahita/project myproject`

Now go to the _myproject_ directory:

`cd myproject`

Continue with [Initiating Installation] (#initiating-installation) from this point.

### Using the Embryo Release code

An Embryo release is the codebase that hasn't been throughly finalized yet and it is still undergoing changes. you may use the embryo code for your project, but you need to be mindful of the fact that it still contain some bugs while it is stable enough that we are using it on our production site [GetAnahita.com](http://www.GetAnahita.com). That is how we discover and fix all the bugs, before we tag the code as a Birth release. The master branch of Anahita always contains the most recent embryo release.

Now clone Anahita repository from the master branch:

`git clone git@github.com:anahitasocial/anahita.git myproject`

change directory to *myproject*

`cd myproject`

Now run the composer command to obtain all the 3rd party libraries that Anahita requires:

`composer update`

Continue with *Initiating Installation* from this point.

## Initiating Installation

If you type _php anahita_ you get a list of all commands available to manage your Anahita installation. If the command didn't work, perhaps the symlink to the anahita command line tool isn't created. In this case run the following command to create a symlink. Otherwise move to the next step which is initiating the installation process.

`ln -s bin/anahita anahita`

In order to initiate the installation process run the following command and provide your database information when it is asked from you:

`php anahita site:init`

The Anahita installation is created in the _PATH-TO-YOUR-DIRECTORY/myproject/www_ directory. You need to configure your server to use this directory as the public directory. 

The first account that is created on this installation becomes the _Super Administrator_ account. Go to the _http://www.YOUR-DOMAIN-NAME.com/people/signup_ and create an account.

Congratulations! You have installed Anahita successfully. Now you need to configure your installation and install some apps.

## Configuration

### Installing Social Apps

Now it is time to extend your Anahita installation with some apps and components. Anahita already comes with some really useful ones. To get a list of them simply type the following command:

`php anahita package:list`

Now in order to install an app, for example the Photos app, type the following command:

`php anahita package:install photos`

You can even provide a list of apps and components in one line. For example to install the Groups, Topics, and Connect apps use the following command:

`php anahita package:install groups topics connect`

In the administration back-end you can go to the _Extend > Components_ and further define whether an app should optionally or always be available on actor profiles (people, groups, etc.). If an app is optionally available, then on each actor profile the app can be enabled under the _Edit Profile > Apps_.

Congratulations! You have just installed some apps and extensions on your Anahita installation.

### Amazon S3 Storage

## Anahita Cli

## Join the Anahita Tribe
If you need any help with the Anahita installation or have general question about Anahita. 
You visit http://GetAnahita.com or follow the Anahita project group at http://www.GetAnahita.com/groups/group/42242-anahita

## Report Bugs or Issues

## Contribute to Anahita
