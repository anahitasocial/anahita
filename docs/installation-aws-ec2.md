# Building an AWS EC2 server and installing Anahita

__Prerequisites:__ you need to be familiar with AWS services such as Route53, Load Balancers (ELB), EC2 Servers, Identity & Access Management (IAM), and Relational Database Service (RDS).

## Installing PHP

Amazon Linux comes with PHP pre-installed. All you need to do is to enable it using the following commands.

`sudo amazon-linux-extras enable php7.4`

`sudo yum clean metadata`

`sudo yum install php-cli php-pdo php-fpm php-json php-mysqlnd`

Now check to see if the PHP 7.4 is installed and working:

`php -v`

Now install a few addditional packages:

`sudo yum install php-mbstring`

`sudo yum install php-gd`

`sudo yum install php-xml`

## Installing NGINX

`sudo amazon-linux-extras enable nginx1`

`sudo yum clean metadata`

`sudo yum -y install nginx`

`sudo systemctl enable --now nginx`

`systemctl status nginx`

## Installing a firewall

`sudo yum install firewalld`

`sudo systemctl start firewalld`

`sudo systemctl enable firewalld`

`sudo systemctl status firewalld`

`sudo firewall-cmd --add-service={http,https} --permanent`

`sudo firewall-cmd --reload`

`sudo firewall-cmd --list-all`

## Configuring PHP FPM Service

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

## Installing Composer

[Composer](https://getcomposer.org/) is a dependancy manager for PHP. This is how you install composer globally on your server:

`cd ~`

`sudo curl -sS https://getcomposer.org/installer | sudo php`

`sudo mv composer.phar /usr/local/bin/composer`

`sudo ln -s /usr/local/bin/composer /usr/bin/composer`

`sudo composer install`

## Installing and configuring Git

We need git to be able to clone and pull Anahita code from the Github repository.

`sudo yum install git -y`

`git version`

### Setup SSH key for Gihub

`ssh-keygen -t ed25519 -C "yourgithubaccountemail@example.com"`

`~ssh-add ~/.ssh/id_ed25519`

`sudo cat /root/.ssh/id_ed25519.pub`

## Setting up a mail server

Anahita sends out a lot of email notifications. The best way is to use a reliable and heavy duty mail service such as [Mailgun](https://www.mailgun.com/) or [Amazon Simple Email Service (SES)](https://docs.aws.amazon.com/ses/latest/dg/setting-up.html) 

## Finally, installing and configuring Anahita

You need to create a MySQL or MariaDB Amazon RDS instance. We don't cover the RDS creation and configuration in this document. Instead, you can read the AWS documentation: [Configuring an Amazon RDS DB instance](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/CHAP_RDS_Configuring.html). Once you have your RDS instance ready, you need to have the following values:

- endpoint
- port
- database
- username
- password 

Now, proceed with installing Anahita in the `var/www/` clone Anahita from your Anahita project repo, or the Anahita main repository:  

`git clone git@github.com:<your-anahita-project-repo-on-github>/<your-anahita-project-name>.git`

`sudo chown ec2-user:ec2-user <your-anahita-project-name> -R`

`cd <your-anahita-project-name>`

This is similar to [installing Anahita on a development machine](./installation-dev.md) start with the following command and follow the instructions to initiate an installation:

`composer update`

`php anahita site:init`






