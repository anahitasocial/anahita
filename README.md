![Anahita social networking platform and framework](https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/logos/homepage_logo.png)

# Anahita

*Version:* 4.6.0 Birth Release

Anahita is a platform and framework for developing open science and knowledge-sharing applications on a social networking foundation. Use Anahita to build:

1. online learning and knowledge-sharing networks
1. information access networks about people, places, and things
1. open science and open data networks
1. online collaboration environments
1. cloud back-end for your mobile apps

Anahita provides nodes and graphs architecture for developing social networking apps.

##### Table of Contents
- [Concepts](#concepts)
- [System Requirements](#system-requirements)
- [Upgrading](#upgrading)
- [Installation on a development machine](#installation-on-a-development-machine)
- [Building an AWS EC2 server and installing Anahita](#building-an-aws-ec2-server-and-installing-anahita)
- [Settings](#anahita-settings)
- [Support](#support)
- [Reporting Bugs & Issues](#reporting-bugs--issues)
- [Contribute to Anahita](#contribute-to-anahita)
- [Follow us, Like us](#follow-us-like-us)
- [Credits](#credits)


## Concepts

### Nodes

1. **actors:** people, groups, or build your custom actor
1. **media:** notes, topics, todos, photos, articles, or build your custom media
1. **hashtags:** all actors, media, and comments are `hashtaggable`  
1. **locations:** all actors and media are `geolocatable`
1. **stories:** updates created by actors for their followers

### Graphs

1. **social graph:** people and groups can be followed by others.
1. **hashtags:** for actors, media, and comments
1. **mentions:** tag people in media and comments
1. **locations:** tag locations in media and actors and search nearby nodes  
1. **notifications:** a person receives email notification whenever a comment is posted on an item to which they are subscribed.
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

The code in the `master` branch is called the **Embryo**. It is what we use to power our website [Anahita.io](https://www.anahita.io) and is constantly changing and evolving. It may contain bugs that we are fixing, or we may add or remove experimental features. Whenever we reach a specific milestone, and the codebase is stable, we package it as a **Birth** release.

## System Requirements

Before you start, please make sure that your server meets the following requirements:

1. Linux or Unix server
1. Nginx or Apache 2.0+
1. MySql 5.7
1. Use PHP version 7.0.0 to 7.4.* for best results.
1. Composer package management. You can download it following the instructions on
http://getcomposer.org/ or just run the following command:

`curl -s http://getcomposer.org/installer | php`

### Important Notes

If you have the Suhosin patch installed on your server, you might get an error. Add this line to your php.ini file to fix it: `suhosin.executor.include.whitelist = tmpl://, file://`

Anahita is installed and managed via shell CLI because this is the most reliable approach, especially after you accumulate large amounts of data in your database.

## Upgrading

If you are upgrading from 4.5.* to 4.6.* you will need client-side applications such as [Anahita React](https://github.com/anahitasocial/anahita-react) as your front-end since Anahita will only provide a RESTful JSON API and no HTML outputs. If you need time to build a client app, you may point your installation to the _legacy_ branch for the time being.

If you are upgrading from 4.3.* to 4.4.*, in `www/configuration.php` file, change `AnConfig` to `AnSiteConfig`.

If you are using any previous 4.* versions of Anahita, [here is how to upgrade](https://www.anahita.io/articles/158983-updating-from-anahita-4-2-to-4-3)


## Installation on a development machine

### Installing a stable package

We call the stable packages _Birth_ releases. Use the following command to create an Anahita project called _myproject_. This command automatically downloads all the required files from the [Anahita GitHub repository](https://github.com/anahitasocial):

`composer create-project anahita/project myproject`

Now go to the _myproject_ directory:

`cd myproject`

Continue with [Initiating Installation] (#initiating-installation) from this point.

### Installing from the master branch

The master branch always contains the _Embryo_ release. Using the following command, clone the Anahita repository from the master branch:

`git clone git@github.com:anahitasocial/anahita.git myproject`

change directory to *myproject*

`cd myproject`

Now run the composer command to obtain all the 3rd party libraries that Anahita requires:

`composer update`

Continue with *Initiating Installation* from this point.

### Initiating Installation

If you type _php anahita_, you get a list of all commands available to manage your Anahita installation. If the command didn't work, perhaps the symlink to the Anahita command line tool isn't created. In this case, run the following command to create a symlink. Otherwise, move to the next step, initiating the installation process.

`ln -s bin/anahita anahita`

To initiate the installation process, run the following command and provide your database information when it asks you:

`php anahita site:init`

The installer creates an Anahita installation in the _PATH-TO-YOUR-DIRECTORY/myproject/www_ directory. You need to configure your server to use this directory as the public directory.

Congratulations! You have installed Anahita successfully. Now you need to signup as a Super Administrator.

### Signing Up The Super Administrator

The first person that is signing up with Anahita becomes the _Super Administrator_. Use the following command to create the first account:

`php anahita site:signup`

Provide a valid _email_ and _username_. You can provide a password, or Anahita automatically creates a strong password for you.

**Congratulations!** You have created the first person and Super Admin account. Point your browser to your Anahita installation and login. 

Next, you will configure your installation and install some apps.


## Building an AWS EC2 server and installing Anahita

__Prerequisites:__ you need to be familiar with AWS services such as Route53, Load Balancers (ELB), EC2 Servers, Identity & Access Management (IAM), and Relational Database Service (RDS).

### Installing PHP

Amazon Linux comes with PHP pre-installed. All you need to do is to enable it using the following commands.

`sudo amazon-linux-extras enable php7.4`

`sudo yum clean metadata`

`sudo yum install php-cli php-pdo php-fpm php-json php-mysqlnd`

Now check to see if the PHP 7.4 is installed and working:

`php -v`

Now install a few additional packages:

`sudo yum install php-mbstring`

`sudo yum install php-gd`

`sudo yum install php-xml`

### Installing NGINX

`sudo amazon-linux-extras enable nginx1`

`sudo yum clean metadata`

`sudo yum -y install nginx`

`sudo systemctl enable --now nginx`

`systemctl status nginx`

#### Configuring NGINX

The following is an example of a configuration file you can use for your EC2 server: 

```
server {
  listen 80;
  server_name <your-anahita-project-server-name>;
  root /var/www/<your-anahita-project-name>/www;
  access_log /var/log/nginx/<your-anahita-project-name>_access.log;
  error_log /var/log/nginx/<your-anahita-project-name>_error.log; 

  location / {
     default_type application/json;
	   client_max_body_size 50M;
	   index index.php index.html;
     try_files $uri $uri/ /index.php?$args;
  }
  
  charset utf-8;
  gzip on;
  gzip_comp_level 3;
  gzip_disable "msie6";
  gzip_min_length 1000;
  gzip_proxied any;
  gzip_types text/xml text/plain application/xml application/json;
  
  location ~ /\. {
      access_log       off;
      log_not_found    off;
      deny             all;
  }
 
  location = /robots.txt {
       allow all;
       log_not_found off;
       access_log off;
  }
  
 location ~* /(?:uploads|files)/.*\.php$ {
        deny all;
 }

  location ~ \.php$ {	  
	      try_files                 $uri =404;
        include                   /etc/nginx/fastcgi_params;
        fastcgi_read_timeout      3600s;
        fastcgi_buffer_size       128k;
        fastcgi_buffers           4 128k;
        fastcgi_param             SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass              unix:/run/php-fpm/www.sock;
        fastcgi_index             index.php;

	       client_max_body_size 50M;

         add_header "Access-Control-Allow-Origin" "https://<your-anahita-project-url>" always;
         add_header "Access-Control-Allow-Methods" "GET, POST, PUT, DELETE, HEAD" always;
         add_header "Access-Control-Allow-Headers" "Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Requested-With" always;
         add_header "Access-Control-Allow-Credentials" "true" always;
  }
}
```

You can run the command `sudo nginx -s reload` every time you edit the config file for the changes to be applied.

### Installing a firewall

`sudo yum install firewalld`

`sudo systemctl start firewalld`

`sudo systemctl enable firewalld`

`sudo systemctl status firewalld`

`sudo firewall-cmd --add-service={http,https} --permanent`

`sudo firewall-cmd --reload`

`sudo firewall-cmd --list-all`

### Configuring PHP FPM Service

Edit the `www.confi` file using vim: 

`sudo vim /etc/php-fpm.d/www.conf`

```
user = nginx
group = nginx
listen = /run/php-fpm/www.sock
listen.acl_users = apache,nginx
pm = ondemand
```

Write and quite vim, and the run the following commands:

`sudo systemctl enable php-fpm`

`sudo systemctl restart php-fpm`

`systemctl status php-fpm`

### Installing PHP Composer

[Composer](https://getcomposer.org/) is a dependancy manager for PHP. This is how you install composer globally on your server:

`cd ~`

`sudo curl -sS https://getcomposer.org/installer | sudo php`

`sudo mv composer.phar /usr/local/bin/composer`

`sudo ln -s /usr/local/bin/composer /usr/bin/composer`

`sudo composer install`

### Installing and configuring Git

We need git to be able to clone and pull Anahita code from the Github repository.

`sudo yum install git -y`

`git version`

#### Setup SSH key for Gihub

`ssh-keygen -t ed25519 -C "yourgithubaccountemail@example.com"`

`~ssh-add ~/.ssh/id_ed25519`

`sudo cat /root/.ssh/id_ed25519.pub`

### Setting up a mail server

Anahita sends out a lot of email notifications. The best way is to use a reliable and heavy-duty mail service such as [Mailgun](https://www.mailgun.com/) or [Amazon Simple Email Service (SES)](https://docs.aws.amazon.com/ses/latest/dg/setting-up.html). Once you setup your SMTP, you need to have the following values for configuring Anahita in the next step:

- username
- password
- host
- port

### Initiating and configuring Anahita

You need to create a MySQL or MariaDB Amazon RDS instance. We don't cover the RDS creation and configuration in this document. Instead, you can read the AWS documentation: [Configuring an Amazon RDS DB instance](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/CHAP_RDS_Configuring.html). Once you have your RDS instance ready, you need to have the following values:

- endpoint
- port
- database
- username
- password 

You can test the database connection from your server using the following command:

`mysql -h <endpoint> -P <port> -u <username> -p`

Enter the password, and then use standard MySQL commands to interact with the database. Once you manage to connect successfully, you can exit.

Installing Anahita on an EC2 instance is similar to [installing Anahita on a development machine](./installation-dev.md) with variations. On EC2, you can clone Anahita under the `var/www/` directory.

From the master branch of the Anahita repository:

`git clone git@github.com:anahitasocial/anahita.git <your-anahita-project-name>`

Or from your custom Anahita project repository:

`git clone git@github.com:<your-anahita-project-repo-on-github>/<your-anahita-project-name>.git`

Now change the permissions of your Anahita project directory:

`sudo chown ec2-user:ec2-user <your-anahita-project-name> -R`

Go in the directory and use composer to get all the required packages:

`cd <your-anahita-project-name>`

`composer update`

Now initiate Anahita; the initiation process asks you for several parameters such as database info, etc. Enter all the values that the prompt message asks you:

`php anahita site:init`

Now signup for the first user account. The first user has Super Admin privileges which is the highest within an Anahita installation:

`php anahita site:signup`

#### Editing the Configuration file

You can use a CLI text editor to edit the configuration.php file in the root directory. There are several parameters that you need to set in this file:

| Parameter | Value | Example | 
| ------- | ----- | ----- |
| $sitename | Website/app name | Anahita |
| $client_domain | base url to your client side webapp | https://www.YourDomain.io |
| $sef_rewrite | Set this to 1 | 1 |
| $debug | If true, Anahita will operate in debugging mode | 0 |
| $error_reporting | Use -1 to turn it off or 30719 to show all warnings | -1 |
| $cors_enabled | Use 1 and set the other cors value. This feature is best if you are using Anahita on your development machine and want to test it with a clientside app. For a production server, set the CORS in the nginx config file, instead. | 0 |
| $mailer | We recommend `smtp` and using an SMTP provider such as Mailgun or AWS SES  | smtp |
| $mailfrom | The email address used in the 'reply to' section of your email notifications | noreply@YourDomain.io |
| $fromname | A name that indicates where the origin of an email notification. |  Anahita Platform |
| $smtp_user | Your SMTP service username | postmaster@sandboxa2a4fc6fe.mailgun.org |
| $smtp_pass| Your SMTP service password | strongpassword |
| $smtp_host | path to the SMTP service host | smtp.mailgun.org |
| $smtp_secure | Use `ssl` | ssl |
| $smtp_port | SMTP service port | 587 |

Write and quit the editor. Now you can set up an AWS Load Balancer (ELB) for your installation and point it to, for example, https://api.YourDomain.io

### Anahita Settings

Before using your Anahita for development or production server, you need to make some configurations. At this point, you need to connect to Anahita using a client-side app such as [Anahita React](https://github.com/anahitasocial/anahita-react). Once you have set up your client-side application, then you can access the _Settings_ as follows:

1. Log in to your Anahita installation as a _Super Administrator_
1. On the main menu, click on the _Settings_

Here are the tabs under the Settings view:

1. **About:** has basic information about your Anahita installation, such as creators and software version.
1. **Apps:** configure installed Anahita apps for the entire site.
1. **Assignments:** configure which actors (People, Groups, etc.) can use what apps (Topics, Photos, etc.)
1. **Plugins:** configure Anahita plugins for the entire site.

### Notifications

Anahita emails out a lot of email notifications. For the email notifications to get sent out, you can set up a cron job on your server to the `yourdomain.com/components/com_notifications/process.php` file. Make sure to go to _Site Settings > Apps > Notifications_ and set **Use Cron** to _Yes_.

There are many articles on the web to show you how to set up a cron job. Depending on your site's number of users and activity, anywhere from 15-minute to 1-hour intervals will work. You will find a suitable interval after monitoring your Anahita installation for a while.   

### Installing Social Apps

Now it is time to extend your Anahita installation with some apps and components. Anahita comes with a list of social apps you can use as they are or use as blueprints for developing your custom apps.

To get a list of available apps, simply type the following command:

`php anahita package:list`

Now to install an app, for example, the Photos app, type the following command:

`php anahita package:install photos`

You can even provide a list of apps and components in one line. For example, to install the Groups, Topics, and Connect apps, use the following command:

`php anahita package:install groups topics connect`

Go to _Site Settings > Assignments_ to define whether an app should optionally or always be available on actor profiles (people, groups, etc.). If an app is optionally available, it can be enabled on each actor profile under the _Edit Profile > Apps_ by the actor profile admin or someone with a higher admin privilege on the Anahita installation.

Congratulations! You have just installed some apps and extensions on your Anahita installation.

### Amazon S3 Storage

In most cases, you wouldn't want to store the uploaded files on your own server. They add up very quickly, making it very difficult to maintain or migrate your Anahita installation. Anahita provides a plugin that stores all uploaded files in the AWS or [Amazon S3](https://aws.amazon.com/s3/) cloud.

Go to _Site Settings > Plugins_ and then from the _type_ list select _Storage_. Edit and disable the _Storage - Local_ plugin by clicking on its name. Edit the _Amazon S3_ plugin using the following settings:

1. **Enabled:** set to _yes_
1. **The folder to store the data:** use _assets_ as the default setting
1. **Bucket:** enter the name of your Amazon S3 bucket
1. **Access Key:** enter your AWS access key
1. **Secret Key:** enter your AWS secret key

Now click _Update_ to store the settings. Try uploading your avatar in the front-end and see if it gets uploaded successfully. Check the image src to make sure it is an AWS URL.

## Support
Anahita has an active and thriving tribe of hobbyists, developers, and entrepreneurs. They are helpful and friendly. So [Join Us](https://www.Anahita.io/join)

**Please Note:** we do not answer questions in an email. If you have any questions, please join the Anahita tribe and post your questions on the [Tribe Support](https://www.anahita.io/groups/107732-tribe-support) group, where others can benefit from the answers too.

## Reporting Bugs & Issues

There are so many ways that you can report a bug:

- open an issue here on our repository
- start a topic on [Anahita Project](https://www.anahita.io/groups/42242-anahita-project) group
- send us a [friendly email](https://www.anahita.io/pages/contact) and tell us how to reproduce the bug

## Contribute to Anahita

Anahita could never be possible without the help of people in our tribe. We need contributors who can help us with testing, finding and fixing bugs and coding. Here is a [complete guideline](https://www.anahita.io/articles/162390-contribute-to-anahita) of how you can contribute to Anahita.

## Follow us, Like us

Follow us on twitter [@anahita_io](https://twitter.com/anahita_io) and like our facebook page [Facebook.com/anahita.io](https://www.facebook.com/anahita.io)

## Credits

Anahita is developed and maintained by [rmdStudio Inc.](https://www.rmdstudio.com), a software development company in Vancouver, Canada. We build client-server architecture applications for scientific, healthcare, and industrial sectors.
