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

![Installing Anahita social apps](https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/gifs/installation/anahita-apps-install.gif)

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