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

### Configuring NGINX

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

You can run the command `sudo nginx -s reload` every time that you edit the config file for the changes to be applied.


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

Anahita sends out a lot of email notifications. The best way is to use a reliable and heavy duty mail service such as [Mailgun](https://www.mailgun.com/) or [Amazon Simple Email Service (SES)](https://docs.aws.amazon.com/ses/latest/dg/setting-up.html). Once you setup your SMTP, you need to have the following values for configuring Anahita in the next step:

- username
- password
- host
- port

## Finally, installing and configuring Anahita

You need to create a MySQL or MariaDB Amazon RDS instance. We don't cover the RDS creation and configuration in this document. Instead, you can read the AWS documentation: [Configuring an Amazon RDS DB instance](https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/CHAP_RDS_Configuring.html). Once you have your RDS instance ready, you need to have the following values:

- endpoint
- port
- database
- username
- password 

You can test the database connection from your server using the following command:

`mysql -h <endpoint> -P <port> -u <username> -p`

Enter password and then you can use standard MySQL commands to interact with the database. Once you managed to connect successfully, you can exit.

Installing Anahita on an EC2 instance is similar to [installing Anahita on a development machine](./installation-dev.md) with some variations. On EC2, you can clone Anahita under the `var/www/` directory.

From the main Anahita repository:

`git clone git@github.com:anahitasocial/anahita.git <your-anahita-project-name>`

Or from your custom Anahita project repository:

`git clone git@github.com:<your-anahita-project-repo-on-github>/<your-anahita-project-name>.git`

Now change the permissions of your anahita project directory:

`sudo chown ec2-user:ec2-user <your-anahita-project-name> -R`

Go in the directory and use composer to get all the required packages:

`cd <your-anahita-project-name>`

`composer update`

Now initiate Anahita; the initiation process asks you for a number of parameters such as database info, etc. Enter all the values that the prompt message asks you:

`php anahita site:init`

Now signup the first user. The first user has Super Admin privileges which is the highest within an Anahita installation:

`php anahita site:signup`













