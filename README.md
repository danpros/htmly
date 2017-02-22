<a href="https://www.htmly.com" target="_blank">![Logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo-big.png)</a>

TUMBLy is a fork of HTMLy with a refurbished admin dashboard. The dashboard is optimized for instant blogging similar to tumblr. 

Please check the [HTMLY](http://htmly.com) website for all further informations. HTMLy is an open source Databaseless Blogging Platform or Flat-File Blog prioritizes simplicity and speed written in PHP.


Changes
----
- Clean and reduced design of the dashboard.
- Main menu is focused on posts, pages and drafts now.
- Post and pages are visually separated now.
- In the main menuy, many less important features are hidden under "tools".
- Startpage of the dashboard is now focused on creating new blog posts.
- Select post types with font awesome icons now.
- Formatting buttons now with font awesome icons.
- You can hide and show format buttons now (with JavaScript).
- Input forms are reduced to title, featured content and main content.
- You can still add all other input fields (categories, tags, date, url) in the config area.
- Several minor changes.



Be Aware of Downsides!!
---------
These changes are hard-coded in the systems-folder of HTMLy. You cannot update to new versions of HTMLy anymore.

The same with the markdown editor "pagedown". The changes are hardcoded, so you cannot update the library anymore. Pagedown hasn't been updated since 2015, so it shouldn't be a big problem.  


Copyright / License
-------------------
For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy is licensed under the GNU General Public License Version 2.0 (or later).

## Screenshots

Dashboard after login. In most cases, you will want to create a new blog post. So show this first. The main-menu at the top is strictly reduced to content creation, now. All admin features (backups, imports, configs, profile) are hidden under "tools". You can display the categories in the configs. In this case, there will be a new menu link for categories next to drafts.

![Dashboard](https://raw.githubusercontent.com/trendschau/tumbly/master/system/resources/images/tumbly-create-posts.png)

The input fields are reduced to the ground: Title, featured content and main content. You can add all other fields (date, tags, categories, url) in the configs. The featured field has now its own toolbar to make the editing process more confortable.

![Dashboard](https://raw.githubusercontent.com/trendschau/tumbly/master/system/resources/images/tumbly-create-image-posts.png)

Pages are completely seperated now. I suppose you will create some static pages if you start your blog, but you will nearly never change them again...

![Dashboard](https://raw.githubusercontent.com/trendschau/tumbly/master/system/resources/images/tumbly-create-page.png)

I ported the lingonberry theme by [Anders Noren](http://www.andersnoren.se) to TUMBLy/HTMLy, because it is very clean and focuses on different post types. I think it is most comfortable for editors, if they find links to edit and add posts in the frontend, if they are logged in.

 ![Dashboard](https://raw.githubusercontent.com/trendschau/tumbly/master/system/resources/images/lingonberry-theme.png)

Have fun!!