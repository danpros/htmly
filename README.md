HTMLy
=====

HTMLy is an open source databaseless blogging platform prioritizing simplicity and speed (Flat-File Blog). And because HTMLy also manage the contents, then it can be referred as a Flat-File CMS.

You do not need to use a VPS to run HTMLy, fairly shared hosting or even a free hosting as long as those hosting already support at least PHP 5.3.

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

Requirements
------------
HTMLy requires PHP 5.3 or greater.

Installations
-------------
Download the latest version, extract it, then upload the extracted files to your server. Make sure the installation folder is writeable by your server.

Configurations
--------------
Rename `config.ini.example` inside `config` folder to `config.ini` or you can create it one than change the site settings there, create `YourUsername.ini` inside `config/users` folder or simply rename the `username.ini.example`, write down your password there.

````
password = YourPassword
````

HTMLy support admin user role either, simply add the following to your choosen user:

````
role = admin
````

Admin role can edit/delete all users posts.

You can login to admin panel at `www.example.com/login`.

Both Online or Offline
----------------------
In addition by using the built-in editor in the admin panel, you can also write it offline and then upload them into `content/username/blog` folder (the username must match with `YourUsername.ini` above). 

For static pages you can upload it to `content/static` folder.

File Naming Convention
----------------------
When you write a blog post and save it via the admin panel, HTMLy automatically create a .md file extension with the following name, example:

````
2014-01-31-12-56-40_tag1,tag2,tag3_databaseless-blogging-platform-flat-file-blog.md
````

Here's the explanation (separated by an underscore):

- `2014-01-31-12-56-40` is the published date. The date format is `yyyy-mm-dd-hh-mm-ss`
- `tag1,tag2,tag3` is the tag, separated by comma
- `databaseless-blogging-platform-flat-file-blog` is the URL

For static pages, we use the following format:

````
about.md
````

That is means if `about` is the URL.

So if you write it offline then you must naming the .md file as above.

Content Title
-------------
If you write it offline, for the title of the post you need to add a title in the following format:

    <!--t Here is the post title t-->

	Paragraph 1

	Paragraph 2 etc.

So wrap the title with HTML comment with `t` for both side.

Demo
----
Visit a real blog powered by HTMLy at [Danlogs](http://www.danlogs.com).

Credit
------
People who give references and inspiration for HTMLy:
* [Martin Angelov](http://tutorialzine.com)

Contribute
----------
1. Fork and edit
2. Submit pull request for consideration

Copyright / License
-------------------
For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy licensed under the GNU General Public License Version 2.0 (or later).