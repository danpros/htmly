<a href="https://www.htmly.com" target="_blank">![Logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo-big.png)</a>

HTMLy is an open source Databaseless Blogging Platform or Flat-File Blog prioritizes simplicity and speed written in PHP. HTMLy can be referred to as Flat-File CMS either since it will also manage your content.

You do not need to use a VPS to run HTMLy, shared hosting or even free hosting should work as long as the host supports at least PHP 5.3.

Demo
----
Visit <a href="https://demo.htmly.com" target="_blank">HTMLy demo</a> as blog.

Features
---------
- Admin Panel
- Markdown editor with live preview and image upload
- Categorization with category and tags (multiple tagging support)
- Static Pages (e.g. Contact Page, About Page)
- Meta canonical, description, and rich snippets for SEO
- Pagination
- Author Page
- Multi author support
- Social Links
- Disqus Comments (optional)
- Facebook Comments (optional)
- Google Analytics
- Built-in Search
- Related Posts
- Per Post Navigation (previous and next post)
- Body class for easy theming
- Breadcrumb
- Archive page (by year, year-month, or year-month-day)
- JSON API
- OPML
- RSS Feed
- RSS 2.0 Importer (basic)
- Sitemap.xml
- Archive and Tag Cloud Widget
- SEO Friendly URLs
- Teaser thumbnail for images and Youtube videos
- Responsive Design
- User Roles
- Online Backup
- File Caching
- Online Update
- Post Draft
- i18n
- Menu builder
- Scheduled posts

Requirements
------------
HTMLy requires PHP 5.3 or greater, PHP-XML package, PHP-INTL package, and PHP-ZIP package for backup feature.

Installations
-------------

Install HTMLy using the source code:

 1. Download the latest version from the [Github repo](https://github.com/danpros/htmly/releases/latest)
 2. Upload and extract the zip file to your web server. You can upload it in the root directory, or in subdirectory such as `htmly`.
 3. Visit your domain. If you extract it in root directory visit `https://www.example.com/install.php` and if in subdirectory visit `https://www.example.com/htmly/install.php`.
 4. Follow the installer to install HTMLy.
 5. The installer will try to delete itself. Please delete the installer manually if the `install.php` still exist. 
 
### Online install

Install HTMLy without downloading the source code and use the online installer:

 1. Download `online-installer.php` from the [latest release](https://github.com/danpros/htmly/releases/latest)
 2. If you upload it in root directory visit `https://www.example.com/online-installer.php` or if in subdirectory visit `https://www.example.com/subdirectory/online-installer.php`.
 3. Follow the installer to install HTMLy.
 4. Please delete the installer manually if the `online-installer.php` and `install.php` still exist.

Configurations
--------------
Set written permission for the `cache` and `content` directories.

In addition, HTMLy support admin user role. To do so, simply add the following line to your choosen user:

````cfg
role = admin
````

Users assigned with the admin role can edit/delete all users posts.

To access the admin panel, add `/login` to the end of your site's URL.
e.g. `www.yoursite.com/login`

### Lighttpd
The following is an example configuration for lighttpd:

````php
$HTTP["url"] =~ "^/config" {
  url.access-deny = ( "" )
}
$HTTP["url"] =~ "^/system/includes" {
  url.access-deny = ( "" )
}
$HTTP["url"] =~ "^/system/admin/views" {
  url.access-deny = ( "" )
}

url.rewrite-once = (
  "^/(themes|system|vendor)/(.*)" => "$0",
  "^/(.*\.php)" => "$0",

  # Everything else is handles by htmly
  "^/(.*)$" => "/index.php/$1"
)
````

### Nginx
The following is a basic configuration for Nginx:

````nginx
server {
  listen 80;

  server_name example.com www.example.com;
  root /usr/share/nginx/html;

  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log error;

  index index.php;

  location ~ /config/ {
     deny all;
  }

  location / {
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME   $document_root$fastcgi_script_name;
        include        fastcgi_params;
  }
}
````

Making a secure password
----------------------
Passwords can be stored in `username.ini` (where "username" is the user's username) in either plaintext, encryption algorithms supported by php `hash` or bcrypt (recommended). To generate a bcrypt encrypted password:
````
$ php -a
> echo password_hash('desiredpassword', PASSWORD_BCRYPT);
````
This will produce a hash which is to be placed in the `password` field in `username.ini`. Ensure that the `encryption` field is set to `password_hash`.

Contribute
----------
1. Fork and edit
2. Submit pull request for consideration

Contributors
----------
- [HTMLy Contributors](https://github.com/danpros/htmly/graphs/contributors)

Copyright / License
-------------------
For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy is licensed under the GNU General Public License Version 2.0 (or later).
