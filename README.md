<a href="https://www.htmly.com" target="_blank">![Logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo-big.png)</a>

HTMLy is an open source databaseless blogging platform prioritizes simplicity and speed written in PHP. 

It uses a unique algorithm to find or list any content based on date, type, category, tag, or author, and it's performance remain fast even if we have ten thousand of posts and hundreds of tags.

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
 
**Note:** If you don't need to log in to the dashboard right away, just rename `config.ini.example` to `config.ini`, delete `install.php`, and your are set. It's always good practice to set the `site.url`
 
### Online install

Install HTMLy without downloading the source code and use the online installer:

 1. Download `online-installer.php` from the [latest release](https://github.com/danpros/htmly/releases/latest)
 2. If you upload it in root directory visit `https://www.example.com/online-installer.php` or if in subdirectory visit `https://www.example.com/subdirectory/online-installer.php`.
 3. Follow the installer to install HTMLy.
 4. Please delete the installer manually if the `online-installer.php` and `install.php` still exist.

Configurations
--------------
Set written permission for the `cache` and `content` directories.

Users assigned with the admin role can edit/delete all users posts.

To access the admin panel, add `/login` to the end of your site's URL.
e.g. `www.yoursite.com/login`


Content Structure
----------------------

Like traditional static pages, even though HTMLy is a dynamic PHP application, most important metadata such as username, category, type, tags, publication date, and slug are in the folder name and filename. 

If you use the dashboard to write your posts, the folder structure and filenames will be set by the dashboard automatically.

The following is an example of a folder and file structure from HTMLy:

```html
content/my-username/blog/my-category/post/2024-01-10-25-35-45_tag1,tag2_my-post-slug.md
```
Here's the explanation:

* `my-username` is the username.
* `my-category` is the content category.
* `post` is the content type. Available content type `post`, `video`, `audio`, `link`, `quote`.
* `2024-01-10-25-35-45` is the published date. The date format is `Y-m-d-H-i-s`
* `tag1,tag2` are the tags, separated by commas
* `my-post-slug` is the URL

**Note:** the filename metadata (post date, tags, slug) separated by an underscore.

With a structure like above, the post can now be visited even though it's just a folder structure and filename.

To claim this content or log in to dashboard, simply create `my-username.ini` in the `config/users/` folder (see `username.ini.example`).

```cfg
;Password
password = yourpassword

; Encryption: Set to clear, and later it will changed to password_hash automatically during login
encryption = clear

;Role
role = admin
```
 

And to add information about the author, create `author.md` in `content/my-username/`, example:

```html
<!--t My Cool Name t-->

Just another HTMLy user
```

Information about `my-category` can be added by creating `my-category.md` inside the `content/data/category/` folder.

```html
<!--t My category title t-->
<!--d My category meta description d-->

This is my category info etc.
```

**Note:** The default category is `Uncategorized` with slug `uncategorized` and you do not need to creating it inside `content/data/category/` folder.

**Note:** Delete the `page` folder inside the `cache` folder to clear the html page cache served to visitors.

**Important:** Every time new content added (post, pages, category), or you make changes that change the folder structure or filenames, simply delete the `index` and `widget` folder inside `cache` folder so that the changes detected by HTMLy. 

**Post Views Limitations:** HTMLy using the filename path as the ID for the post/page views counter. So if you edit a post/page without using the dashboard which results in changes to the folder structure or filename, then you must edit `views.json` in the `content/data/` folder manually to update to correct path.

Static pages
------------

For static pages, use the following format:

```html
content/static/about.md
```

In the example above, the `about.md` creates the URL: `www.yourblog.com/about`

Thus, if you write/create files locally, you must name the .md file in the format above.

For static subpages, use the following format:

```html
content/static/about/me.md
```

This will create the URL: `www.yourblog.com/about/me`

An example pages/subpages content looks like:

```html
<!--t My page title t-->
<!--d My page meta description d-->

This is my page info etc.
```

Content Tags
-------------
If you are writing locally, you need specify the content tags below:

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
