# metahub

ALL YOUR PULL-REQUEST ARE BELONG TO US.


## SYSTEM REQUIREMENTS

 - Apache 2.2 or later
 - PHP 5.3 or later
 - MySQL 5.1 or later


## INSTALL

1. Get all metahub files from github repository and place them to 'metahub' directory.
2. Create logical database 'metahub' on MySQL and execute 'metahub/create.sql'.
3. Create 'metahub/dbauth' directory and execute this command: <br>
   ```echo "<database-user>:<password>" > metahub/dbauth/admin```
4. Create 'metahub/apiauth/github_project_owner' file contains github user/organization name.
5. Put github access token in 'metahub/apiauth/github_accesstoken'. see: http://developer.github.com/v3/oauth/

sample httpd.conf
```
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName metahub.local

        DocumentRoot /path/to/metahub/www/
        <Directory /path/to/metahub/www/>
                Options Indexes FollowSymLinks -MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

Listen 10080
NameVirtualHost 127.0.0.1:10080
<VirtualHost 127.0.0.1:10080>
        ServerAdmin webmaster@localhost
        ServerName localhost
        DocumentRoot /var/www
        CustomLog ${APACHE_LOG_DIR}/proxy.log combined
        ProxyRequests On
        <Proxy *>
                Order Deny,Allow
                Deny from all
                Allow from 127.0.0.1
        </Proxy>
</VirtualHost>
```


