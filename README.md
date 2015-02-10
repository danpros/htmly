HTMLy
=====

HTMLy is an open source databaseless blogging platform prioritizing simplicity and speed (Flat-File Blog). And because HTMLy also manage the contents, then it can be referred as a Flat-File CMS.

You do not need to use a VPS to run HTMLy, shared hosting or even free hosting should work as long as the host supports at least PHP 5.3.

Features
---------
- Admin panel
- Markdown editor with live preview
- Categorization with tags (multi tags support)
- Static pages Eg. for contact page
- Meta canonical, description, and rich snippets for SEO
- Pagination
- Author page
- Multi author support
- Social links
- Disqus Comments (optional)
- Facebook Comments (optional)
- Google Analytics
- Built-in search
- Related posts
- Per post navigation (previous and next post)
- Body class for easy theming
- Breadcrumb
- Archive page (by year, year-month, or year-month-day)
- JSON API
- OPML
- RSS Feed
- RSS 2.0 Importer (basic)
- Sitemap.xml
- Archive and tag cloud widget
- SEO friendly URLs
- Teaser thumbnail for images and Youtube videos
- Responsive design
- Lightbox
- User role
- Online backup
- File cache
- Auto Update

Requirements
------------
HTMLy requires PHP 5.3 or greater.

Installations
-------------
if you have openssl on your server, use the [Installer](https://github.com/Kanti/htmly-installer/releases/latest). read the [Instructions](https://github.com/Kanti/htmly-installer/blob/master/README.md#htmly-installerphp).
If you don't have openssl, [download](https://github.com/danpros/htmly/releases/latest) the latest version, extract it, then upload the extracted files to your server. Make sure the installation folder is writeable by your server.

Configurations
--------------
Rename `config.ini.example` inside the `config` folder to `config.ini` (or you can create a new `config/config.ini` file) then change the site settings there.

Create `YourUsername.ini` inside the `config/users` folder or simply rename the `username.ini.example` file and write down your password there:

````cfg
password = YourPassword
````

HTMLy support admin user role either, simply add the following to your choosen user:

````cfg
role = admin
````

A user with the admin role can edit/delete all users' posts.

You can login to admin panel at `www.example.com/login`.

### Lighttpd
Here a example configuration

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
Here a basic configuration for nginx.

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

Both Online or Offline
----------------------
In addition by using the built-in editor in the admin panel, you can also write markdown files offline and then upload them (see naming convention below) into the `content/username/blog` folder (the `username` must match `YourUsername.ini` above). 

For static pages you can upload it to the `content/static` folder.

File Naming Convention
----------------------
When you write a blog post and save it via the admin panel, HTMLy automatically create a .md file extension with the following name, example:

````
2014-01-31-12-56-40_tag1,tag2,tag3_databaseless-blogging-platform-flat-file-blog.md
````

Here's the explanation (separated by an underscore):

- `2014-01-31-12-56-40` is the published date. The date format is `yyyy-mm-dd-hh-mm-ss`
- `tag1,tag2,tag3` are the tags, separated by commas
- `databaseless-blogging-platform-flat-file-blog` is the URL
	
For static pages, we use the following format:

````
content/static/about.md
````

That means the URL is `about`.

So if you write it offline then you must name the .md file as above.

For static sub pages, we use the following format:

````
content/static/about/me.md
````

That means the URL is `about/me`.

Content Title
-------------
If you write it offline, for the title of the post you need to add a title in the following format:
```html
<!--t Here is the post title t-->

Paragraph 1

Paragraph 2 etc.
```
So wrap the title with HTML comment with `t` for both side.

Demo
----
Visit [HTMLy Demo](http://demo.htmly.com).

Credit
------
People who give references and inspiration for HTMLy:
* [Martin Angelov](http://tutorialzine.com)

Contribute
----------
1. Fork and edit
2. Submit pull request for consideration

Contributors
----------
- [danpros](https://github.com/danpros) - [Weblog](http://www.danpros.com)
- [Kanti](https://github.com/Kanti) - [Weblog](https://kanti.de)
- [fahmi182](https://github.com/fahmi182) - [Weblog](http://ifahmi.com)
- [fanningert](https://github.com/fanningert) - [Weblog](http://thomas.fanninger.at)
- [BlackCodec](https://github.com/BlackCodec)
- [mlncn](https://github.com/mlncn)

Copyright / License
-------------------
For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy licensed under the GNU General Public License Version 2.0 (or later).
