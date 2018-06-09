# Flextype
![Version](https://img.shields.io/badge/version-0.5.0-brightgreen.svg?style=flat-square)
![MIT License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

Flextype is Open Source, fast and flexible file-based Content Management System.  
That's Easy to install, upgrade and use. Flextype provides amazing API's for plugins, themes and core developers!

## FEATURES

#### Simple
Easy to install, upgrade and use.  
No installation needed, just copy files to your server!  

#### Fast
Flextype is realy fast and lightweight!  
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
PHP 7.1.3 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)  

## INSTALLATION

#### Using (S)FTP

[Download the latest version.](http://flextype.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 777, so they are readable and writable by Flextype:  
* `site/`

#### Using Composer

You can easily install Flextype with Composer.

```
composer create-project flextype/flextype
```

Also you may need to install node_modules libs for default Simple Theme  

```
cd /flextype/site/themes/simple  
npm install
gulp
```


## COMMUNITY
Flextype is open source, community driven project, and maintained by community!

* [Forum](http://forum.flextype.org)
* [Github Repository](https://github.com/flextype/flextype)
* [Gitter](https://gitter.im/flextype/flextype)


## NO LIMITS
With Flextype you can create any project you whant.

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
* Help on the [Forum.](http://forum.flextype.org)
* Develop a new plugin.
* Create a new theme.
* Find and [report issues.](https://github.com/flextype/flextype/issues)
* Link back to [Flextype](http://flextype.org).
* [Donate to keep Flextype free.](http://flextype.org/about/sponsors)


## LINKS
- [Site](http://flextype.org)
- [Forum](http://forum.flextype.org)
- [Documentation](http://flextype.org/documentation)


## LICENSE
See [LICENSE](https://github.com/flextype/flextype/blob/master/LICENSE.md)
