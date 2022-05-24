# Installing Anahita on a development machine

## Installing a stable package

![Installing Anahita using the Birth release code](https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/gifs/installation/anahita-installation-birth.gif)

Stable packages are called _Birth_ releases. Use the following command to create an Anahita project called _myproject_. This command automatically downloads all the required files from the [Anahita GitHub repository](https://github.com/anahitasocial):

`composer create-project anahita/project myproject`

Now go to the _myproject_ directory:

`cd myproject`

Continue with [Initiating Installation] (#initiating-installation) from this point.

## Installing from the master branch

![Installing Anahita using the Embryo release code](https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/gifs/installation/anahita-installation-embryo.gif)

The master branch always contains the _Embryo_ release. Using the following command, clone the Anahita repository from the master branch:

`git clone git@github.com:anahitasocial/anahita.git myproject`

change directory to *myproject*

`cd myproject`

Now run the composer command to obtain all the 3rd party libraries that Anahita requires:

`composer update`

Continue with *Initiating Installation* from this point.

## Initiating Installation

![Initiating Anahita installation](https://s3.ca-central-1.amazonaws.com/production.anahita.io/media/gifs/installation/anahita-installation-init.gif)

If you type _php anahita_ you get a list of all commands available to manage your Anahita installation. If the command didn't work, perhaps the symlink to the anahita command line tool isn't created. In this case run the following command to create a symlink. Otherwise move to the next step which is initiating the installation process.

`ln -s bin/anahita anahita`

In order to initiate the installation process run the following command and provide your database information when it is asked from you:

`php anahita site:init`

The Anahita installation is created in the _PATH-TO-YOUR-DIRECTORY/myproject/www_ directory. You need to configure your server to use this directory as the public directory.

Congratulations! You have installed Anahita successfully. Now you need to signup as a Super Administrator.

## Signing Up The Super Administrator

The first person that is signing up with Anahita is recognized as the _Super Administrator_. Use the following command to sign up the first person:

`php anahita site:signup`

Provide a valid _email_ and _username_. You can either provide a password or Anahita creates a strong password for you.

**Congratulations!** You have created the first person and Super Admin account. Point your browser to your Anahita installation and login. 

Next, you will configure your installation and install some apps.