# WSP (Wilame's Syntax for PHP)
A new syntax for beginners in PHP!
Use WSP to learn or teach basic syntax semantics

The WSP has the practicality of HTML and the functionality of PHP, from the basic to advanced.
Note: Use this on PHP 7.x or more

# How to install
> Download "wspsyntax.php" and save it in your server (can be on www root)
> Go to php.ini, set or create "auto_prepend_file" and give the file dir and name
like this:
```
auto_prepend_file = "/usr/www/wspsyntax.php"
```

# Activate PHP on .wsp files
> Go to .htaccess and put this:
```
php_value auto_prepend_file "/usr/www/wspsyntax.php"
AddHandler application/x-httpd-php .wsp
DirectoryIndex index.wsp
```

> Or go to httpd.conf and put this:
```
<FilesMatch \.wsp$>
  SetHandler application/x-httpd-php
</FilesMatch>
<IfModule dir_module>
    DirectoryIndex index.html index.php index.wsp
</IfModule>
```
# At this point... We are ready :v
See Wiki -> Basics to learn the syntax for coding :p
