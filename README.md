# [![HTMLy logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/htmly-small.png)](https://www.htmly.com/)

HTMLy is an open source databaseless blogging platform prioritizes simplicity and speed written in PHP. 

It uses a unique algorithm to find or list any content based on date, type, category, tag, or author, and it's performance remain fast even if we have ten thousand of posts and hundreds of tags.

## Requirements

HTMLy requires PHP 7.2 or greater, PHP-Mbstring, PHP-XML, PHP-INTL, PHP-GD, and PHP-ZIP package for backup feature.

## Installations

Install HTMLy using the source code:

 1. Download the latest version from the [Github repo](https://github.com/danpros/htmly/releases/latest)
 2. Upload and extract the zip file to your web server. You can upload it in the root directory, or in subdirectory such as `htmly`.
 3. Visit your domain. If you extract it in root directory visit `https://www.example.com/install.php` and if in subdirectory visit `https://www.example.com/htmly/install.php`.
 4. Follow the installer to install HTMLy.
 5. The installer will try to delete itself. Please delete the installer manually if the `install.php` still exist. 
 
**Note:** If you don't need to log in to the dashboard, just rename `config.ini.example` to `config.ini`, delete `install.php`, and you are set. It's always good practice to set the `site.url`
 
### Online install

Install HTMLy without downloading the source code and use the online installer:

 1. Download `online-installer.php` from the [latest release](https://github.com/danpros/htmly/releases/latest)
 2. If you upload it in root directory visit `https://www.example.com/online-installer.php` or if in subdirectory visit `https://www.example.com/subdirectory/online-installer.php`.
 3. Follow the installer to install HTMLy.
 4. Please delete the installer manually if the `online-installer.php` and `install.php` still exist.

## Configurations

Set written permission for the `cache` and `content` directories.

Users assigned with the admin role can edit/delete all users posts.

To access the admin panel, add `/login` to the end of your site's URL.
e.g. `www.yoursite.com/login`

## Resources

 - Homepage: [HTMLy Homepage](https://www.htmly.com/)
 - Documentation: [HTMLy Docs](https://docs.htmly.com/)
 - Themes: [HTMLy Themes](https://www.htmly.com/theme/)
 - Demo: [HTMLy Demo](http://demo.htmly.com/)
 - Repository: [Github](https://github.com/danpros/htmly/)

## Contribute

1. Fork and edit
2. Submit pull request for consideration

## Contributors

Thank you to our [contributors](https://github.com/danpros/htmly/graphs/contributors)

## Sponsors

Support this project by becoming a [sponsor](https://github.com/sponsors/danpros)

## Copyright / License

For copyright notice please read [COPYRIGHT.txt](https://github.com/danpros/htmly/blob/master/COPYRIGHT.txt). HTMLy is licensed under the GNU General Public License Version 2.0 (or later).
