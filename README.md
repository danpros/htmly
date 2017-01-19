<a href="https://www.htmly.com" target="_blank">![Logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo-big.png)</a>

HTMLy is an open source databaseless web publishing platform that prioritizes simplicity and speed. HTMLy can be referred to as a Flat-File Blog or Flat-File CMS, since it will also manage your content.

You do not need to use a VPS to run HTMLy, shared hosting or even [free hosting](https://github.com/danpros/htmly#openshift) should work as long as the host supports at least PHP 5.3.

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
- Auto Update
- Post Draft

Requirements
------------
HTMLy requires PHP 5.3 or greater and php-xml package.

Installations
-------------
If you have an OpenSSL enabled server (usually enabled by default), use the [installer](https://github.com/Kanti/htmly-installer/releases/latest) and read the following [instructions](https://github.com/Kanti/htmly-installer/blob/master/README.md#htmly-installerphp) to get started. If you don't have OpenSSL, please download the latest version, extract it, then upload the extracted files to your server. Also, make sure the installation folder is writeable by your server.

Configurations
--------------
Rename `config.ini.example` inside the `config` folder to `config.ini` (or you can create a new `config/config.ini` file) then change the site settings there.

Create `YourUsername.ini` inside the `config/users` folder or simply rename the `username.ini.example` file and write down your password there:

````cfg
password = YourPassword
````

In addition, HTMLy support admin user role. To do so, simply add the following line to your choosen user:

````cfg
role = admin
````

Users assigned with the admin role can edit/delete all users' posts.

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

OpenShift
---------
Need a free server to test HTMLy? try [OpenShift](https://www.openshift.com) using the [HTMLy OpenShift QuickStart](https://github.com/danpros/htmly-openshift) for easy deployment.

**Quick install:**

[![Click to install OpenShift](http://launch-shifter.rhcloud.com/launch/light/Click to install.svg)](https://hub.openshift.com/quickstarts/deploy/219-htmly)


Making a secure password
----------------------
Passwords can be stored in `username.ini` (where "username" is the user's username) in either plaintext, encryption algorithms supported by php `hash` or bcrypt (recommended). To generate a bcrypt encrypted password:
````
$ php -a
> echo password_hash('desiredpassword', PASSWORD_BCRYPT);
````
This will produce a hash which is to be placed in the `password` field in `username.ini`. Ensure that the `encryption` field is set to `bcrypt`.


Both Online or Offline
----------------------
The built-in editor found in the admin panel, also provides you the ability to write to Markdown files offline by uploading them (see naming convention below) into the `content/username/blog/category/type/`:

* `username` must match `config/users/username.ini`.
* `category` must match the `category.md` inside `content/data/category/category.md` except the `uncategorized` category.
* `type` is the content type. Available content type `post`, `video`, `audio`, `link`, `quote`.

For static pages you can upload it to the `content/static` folder.

Category
--------
The default category is `Uncategorized` with slug `uncategorized` and you do not need to creating it inside `content/data/category/` folder. But if you write it offline and want to assign new category to specific post you need to creating it first before you can use those category, example `content/data/category/new-category.md` with the following content:

```html
<!--t New category title t-->
<!--d New category meta description d-->

New category info etc. 
```` 
The slug for the new category is `new-category` (htmly removing the file extension). And for full file directory:
````
content/username/new-category/post/file.md
````

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
	
For static pages, use the following format:

````
content/static/about.md
````

In the example above, the `/about.md` creates the URL: `www.yourblog.com/about`

Thus, if you write/create files offline, you must name the .md file in the format above.

For static subpages, use the following format:

````
content/static/about/me.md
````

This will create the URL: `www.yourblog.com/about/me`

Content Tags
-------------
If you are writing offline, you need specify the content tags below:

**Title**
```html
<!--t Title t-->
````  

**Meta description**
```html
<!--d The meta description d-->
```` 

**Tags**

This is just the tags display and for the slug is in the filename.
```html
<!--tag Tag1,Tag2 tag-->
```` 

**Featured image**

Post with featured image.
```html
<!--image http://www.example.com/image-url/image.jpg image-->
```` 

**Featured youtube video**

Post with featured youtube video.
```html
<!--video https://www.youtube.com/watch?v=xxxxxxx video-->
```` 

**Featured soundcloud audio**

Post with featured soundcloud audio.
```html
<!--audio https://soundcloud.com/xxxx/audio-url audio-->
```` 

**Featured link**

Post with featured link.
```html
<!--link https://github.com/danpros/htmly link-->
```` 

**Featured quote**

Post with featured quote.
```html
<!--quote Premature Optimization is The Root of All Evil quote-->
```` 

**Example**

Example of how your post would look like:
```html
<!--t Here is the post title t-->
<!--d The meta description d-->
<!--tag Tag1,Tag2 tag-->
<!--video https://www.youtube.com/watch?v=xxxxxxx video-->

Paragraph 1

Paragraph 2 etc.
```

Credit
------
* [Martin Angelov](http://tutorialzine.com)
* [Xiaoying Riley](http://themes.3rdwavemedia.com)

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
- [Tea23](https://github.com/Tea23)
- [greenphp](https://github.com/greenphp)

Copyright / License
-------------------
For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy is licensed under the GNU General Public License Version 2.0 (or later).
