![preview](/site/plugins/admin/preview.png)

<p align="center">
<a href="https://github.com/flextype/flextype/releases"><img alt="Version" src="https://img.shields.io/github/release/flextype/flextype.svg?label=version"></a> <a href="https://github.com/flextype/flextype"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a> <a href="https://github.com/flextype/flextype"><img src="https://img.shields.io/github/downloads/flextype/flextype/total.svg?colorB=blue" alt="Total downloads"></a> <a href="https://crowdin.com/project/flextype"><img src="https://d322cqt584bo4o.cloudfront.net/flextype/localized.svg" alt="Crowdin"></a> <a href="https://scrutinizer-ci.com/g/flextype/flextype?branch=master"><img src="https://img.shields.io/scrutinizer/g/flextype/flextype.svg?branch=master" alt="Quality Score"></a> <a href="https://discordapp.com/invite/CCKPKVG"><img src="https://img.shields.io/discord/423097982498635778.svg?logo=discord&colorB=728ADA&label=Discord%20Chat" alt="Discord"></a>

</p>

<h1 align="center">Supporting Flextype</h1>

Flextype is an MIT-licensed open source project and completely free to use.

However, the amount of effort needed to maintain and develop new features for the project is not sustainable without proper financial backing. You can support it's ongoing development by being a backer or a sponsor:

- [Become a backer or sponsor on Patreon](https://www.patreon.com/awilum).
- [One-time donation via PayPal, QIWI, Sberbank, Yandex](http://flextype.org/en/one-time-donation)

## INTRODUCTION

Flextype was **founded in March 2018** as lightweight alternative to other heavy and outdated CMS. Many people use complex solutions for simple pages, unnecessarily. Building this content management system, we focused on simplicity - even novice webmaster adapt his template and writes his own plugin. To achieve this, we implemented a simple but powerful API's.

With Flextype, you are in complete control. Design your data structure the way you want. Update and share your data with others and teams using version control. Flextype does not require MySQL database, because all the data are collected in a simple files.  Perfect portability when changing your hosting provider. Just copy all the files from one account to another.

The underlying architecture of Flextype is built using well established and best-in-class technologies. This is to ensure that Flextype is simple to use and easy to extend. Some of these key technologies include:

* [Slim PHP](http://www.slimframework.com): Framework for powerful web applications and APIs.
* [Twig Templating](https://twig.symfony.com): Flexible, fast, and secure
template engine for PHP.
* [Doctrine Project](https://www.doctrine-project.org): A set of decoupled and reusable PHP libraries for powerful web applications.
* [The PHP League](https://thephpleague.com): The League of Extraordinary PHP Packages well tested and using modern coding standards.
* [Respect Validation](https://respect-validation.readthedocs.io/): The most awesome validation engine ever created for PHP.
* [Thunderer Shortcode](https://github.com/thunderer/Shortcode): Advanced shortcode (BBCode) parser and engine for PHP.

## REQUIREMENTS

#### System requirements
Make sure your server meets the following requirements.

- Webserver (Apache with Mod Rewrite)
- PHP 7.2.0 or higher

#### PHP extensions
Flextype needs the following PHP extensions to be enabled:

- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) module for full UTF-8 support.
- PHP [gd](http://php.net/manual/en/book.image.php) or [ImageMagick](http://php.net/manual/en/book.imagick.php) module for image processing.
- PHP [json](https://php.net/manual/en/book.json.php) module for JSON manipulation.
- PHP [Fileinfo](https://www.php.net/manual/en/book.fileinfo.php)
- PHP [SPL](https://www.php.net/manual/en/book.spl.php)
- PHP [DOM](https://www.php.net/manual/ru/class.domdocument.php)

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

[Download the latest version.](http://flextype.org/en/downloads)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 755(or 777), so they are readable and writable by Flextype:  
- `site/`

#### Using Composer

You can easily install Flextype with Composer.

```
composer create-project flextype/flextype
```

Install vendor libs for Flextype
```
composer install
```

Install vendor libs for Default Theme

```
cd site/themes/default
npm install
gulp
```

Install vendor libs for Admin Panel plugin

```
cd site/plugins/admin
composer install
npm install
gulp
```

Install vendor libs for Site plugin

```
cd site/plugins/site
composer install
npm install
gulp
```

## COMMUNITY
Flextype is open source, community driven project, and maintained by community!

- [Github Repository](https://github.com/flextype/flextype)
- [Discord](https://discord.gg/CCKPKVG)
- [Forum](http://forum.flextype.org)
- [Vkontakte](https://vk.com/flextype)
- [Twitter](https://twitter.com/getflextype)

## CONTRIBUTE
Flextype is an open source project and community contributions are essential to its growing and success. Contributing to the Flextype is easy and you can give as little or as much time as you want.

- Help on the [Communities.](http://flextype.org/en/community)
- Develop a new plugin.
- Create a new theme.
- Find and [report issues.](https://github.com/flextype/flextype/issues)
- Link back to [Flextype](http://flextype.org).
- [Donate to keep Flextype free.](http://flextype.org/en/about)
- [Join Flextype International Translator Team](https://crowdin.com/project/flextype/invite)

## LICENSE
[The MIT License (MIT)](https://github.com/flextype/flextype/blob/master/LICENSE.txt)
Copyright (c) 2018-2019 [Sergey Romanenko](https://github.com/Awilum)
