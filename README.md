# Flextype
[![Discord](https://img.shields.io/discord/423097982498635778.svg?logo=discord&colorB=728ADA&label=Discord%20Chat&style=flat-square)](https://discordapp.com/invite/CCKPKVG)
![Version](https://img.shields.io/badge/version-0.8.3-brightgreen.svg?style=flat-square)
![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

![preview](/site/plugins/admin/preview.png)

Flextype is Open Source, fast and flexible file-based Content Management System.  
That's Easy to install, upgrade and use. Flextype provides amazing API's for plugins, themes and core developers!

## FEATURES

#### Simple
Easy to install, upgrade and use.  
No installation needed, just copy files to your server!  

#### Fast
Flextype is really fast and lightweight!  
No database required, flat files only!  

#### Flexible
Flextype provides amazing API for plugins, themes and core developers!  

#### Easy editing
Use your favorite editor to write your content with plain HTML and Flextype Shortcodes.  

#### Dynamic Content Types
The flat-file nature of Flextype lets you define custom fields for any of your pages.  

#### Open Source
Flextype is an open-source project licensed under the MIT LICENSE to set the world free!  

## REQUIREMENTS

#### System requirements
Make sure your server meets the following requirements.

- Webserver (Apache with Mod Rewrite)
- PHP 7.1.3 or higher

#### PHP extensions
Flextype needs the following PHP extensions to be enabled:

- Multibyte String
- SPL

Although it is optional, we strongly recommend enabling the following PHP extensions:
APC, APCu, XCache, Memcached, or Redis for better performance.

#### Browser requirements
The admin panel of Flextype is compatible with:

Windows and macOS
- Chrome 29 or later
- Firefox 28 or later
- Safari 9.0 or later
- Microsoft Edge

Mobile
- iOS: Safari 9.1 or later
- Android: Chrome 4.4 or later

## INSTALLATION

#### Using (S)FTP

[Download the latest version.](http://flextype.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 755(or 777), so they are readable and writable by Flextype:  
* `site/`

#### Using Composer

You can easily install Flextype with Composer.

```
composer create-project flextype/flextype
```

Install vendor libs for Default Theme

```
composer install
cd site/themes/default
npm install
gulp
```

Install vendor libs for Admin Panel

```
cd site/plugins/admin
composer install
npm install
gulp
```

## COMMUNITY
Flextype is open source, community driven project, and maintained by community!

* [Github Repository](https://github.com/flextype/flextype)
* [Discord](https://discord.gg/CCKPKVG)
* [Vkontakte](https://vk.com/flextype)
* [Twitter](https://twitter.com/getflextype)


## NO LIMITS
With Flextype you can create any project you want.

* Business site
* Landing page
* Personal site
* Portfolio
* Product site
* Documentation
* Personal resume
* Blog


## CONTRIBUTE
Flextype is an open source project and community contributions are essential to its growing and success. Contributing to the Flextype is easy and you can give as little or as much time as you want.

* Help on the [Communities.](http://flextype.org/documentation/basics/getting-help)
* Develop a new plugin.
* Create a new theme.
* Find and [report issues.](https://github.com/flextype/flextype/issues)
* Link back to [Flextype](http://flextype.org).
* [Donate to keep Flextype free.](http://flextype.org/about/sponsors)


## LINKS
- [Site](http://flextype.org)
- [Documentation](http://flextype.org/documentation)


## LICENSE
See [LICENSE](https://github.com/flextype/flextype/blob/master/LICENSE.txt)
