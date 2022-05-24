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






