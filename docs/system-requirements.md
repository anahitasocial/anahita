# System Requirements

Before you start please make sure that your server meets the following requirements:

1. Linux or unix server
1. Nginx or Apache 2.0+
1. MySql 5.7
1. Use PHP version 7.0.0 to 7.4.* for best results.
1. Composer package management. You can download it following the instructions on
http://getcomposer.org/ or just run the following command:

`curl -s http://getcomposer.org/installer | php`

## Important Notes

If you have the suhosin patch installed on your server you might get an error. Add this line to your php.ini file to fix it: `suhosin.executor.include.whitelist = tmpl://, file://`

Anahita is installed and managed via command line interface, because this is the most reliable approach especially after you accumulate large amounts of data in your database.