<img src="https://images2.imgbox.com/49/8d/4ipHqUcj_o.jpg" alt="Flextype" align="center">

<p align="center">
<a href="https://github.com/flextype/flextype/releases"><img alt="Version" src="https://img.shields.io/github/release/flextype/flextype.svg?label=version&color=black"></a> <a href="https://github.com/flextype/flextype"><img src="https://img.shields.io/badge/license-MIT-blue.svg?color=black" alt="License"></a> <a href="https://github.com/flextype/flextype"><img src="https://img.shields.io/github/downloads/flextype/flextype/total.svg?color=black" alt="Total downloads"></a> <a href="https://scrutinizer-ci.com/g/flextype/flextype?branch=master"><img src="https://img.shields.io/scrutinizer/g/flextype/flextype.svg?branch=master&color=black" alt="Quality Score"></a> <a href="https://flextype.org/en/discord"><img src="https://img.shields.io/discord/423097982498635778.svg?logo=discord&color=black&label=Discord%20Chat" alt="Discord"></a>
</p>
<br>

## INTRODUCTION

**Flextype** is an open-source **Hybrid Content Management System** with the freedom of a headless CMS and with the full functionality of a traditional CMS. Building this Content Management System, we focused on simplicity. To achieve this, we implemented a simple but powerful API's.

With Flextype, you are in complete control. Design your data structure the way you want. Update and share your data with others and teams using version control. Flextype does not require MySQL database, because all the data are collected in a simple files. Perfect portability when changing your hosting provider. Just copy all the files from one server to another.

The underlying architecture of Flextype is built using well established and best-in-class technologies. This is to ensure that Flextype is simple to use and easy to extend. Some of these key technologies include:

* [Slim PHP](http://www.slimframework.com): Framework for powerful web applications and APIs.
* [Doctrine Project](https://www.doctrine-project.org): A set of decoupled and reusable PHP libraries for powerful web applications.
* [The PHP League](https://thephpleague.com): The League of Extraordinary PHP Packages well tested and using modern coding standards.
* [Respect Validation](https://respect-validation.readthedocs.io/): The most awesome validation engine ever created for PHP.
* [Thunderer Shortcode](https://github.com/thunderer/Shortcode): Advanced shortcode (BBCode) parser and engine for PHP.
* [Markdown & Parsedown](https://github.com/erusev/parsedown): Easy content creation using Markdown Syntax.
* [YAML](https://yaml.org): Human friendly data serialization format for simple configuration.

## REQUIREMENTS

#### SYSTEM REQUIREMENTS
Make sure your server meets the following requirements.

* Webserver (Apache with Mod Rewrite)
* PHP 7.3.0 or higher

#### PHP EXTENSIONS
Flextype needs the following PHP extensions to be enabled:

* PHP [mbstring](http://php.net/manual/en/book.mbstring.php) module for full UTF-8 support.
* PHP [gd](http://php.net/manual/en/book.image.php) or [ImageMagick](http://php.net/manual/en/book.imagick.php) module for image processing.
* PHP [json](https://php.net/manual/en/book.json.php) module for JSON manipulation.
* PHP [Fileinfo](https://www.php.net/manual/en/book.fileinfo.php)
* PHP [SPL](https://www.php.net/manual/en/book.spl.php)
* PHP [DOM](https://www.php.net/manual/ru/class.domdocument.php)

Although it is optional, we strongly recommend enabling the following PHP extensions:
APC, APCu, XCache, Memcached, or Redis for better performance.

## QUICK INSTALLATION

1. [Download the latest version of Flextype](https://flextype.org/en/downloads).
2. Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, create directory <code>/project</code> and be sure to chmod the following directory to <code>755</code> (or <code>777</code>), so it is readable and writable by Flextype.<br>
3. Create Rest API's tokens for your project using this [webpage](https://flextype.org/en/api-token-generator).

[read the documentation](https://github.com/flextype/plugins)

## COMMUNITY
Flextype is open source, community driven project, and maintained by community!

#### [Discord](https://flextype.org/en/discord)

Got a question about setting up or using Flextype? We'll do our best to help you out. Also here you may start discussions about core, plugin and themes development.

#### [Github](https://github.com/flextype)

If you want to report a bug or contribute your ideas, you can use the Flextype GitHub Issues tracker.

#### [Twitter](https://twitter.com/getflextype)

Follow Flextype on Twitter to get real-time news regarding the development and all events we are attending.

#### [Vkontakte](https://vk.com/flextype)

Russian Flextype Community!

## SUPPORTING FLEXTYPE

Flextype is an open source project and community contributions are essential to its growing and success.

Contributing to the Flextype is easy and you can give as little or as much time as you want.

### FINANCIAL SUPPORT

Flextype is an MIT-licensed open source project and completely free to use.

However, the amount of effort needed to maintain and develop new features for the project is not sustainable without proper financial backing.

You can support it's ongoing development by being a project backer or a sponsor:
* [Become a backer or sponsor on Patreon](https://www.patreon.com/awilum).
* [One-time donation via PayPal, QIWI, Sberbank, Yandex](https://flextype.org/en/one-time-donation)
* [Visit our Sponsors & Backers page](https://flextype.org/en/sponsors)

### PLATFORM CONTRIBUTIONS

Another excellent way to help out is by contributing your time or services.

#### TRANSLATION

We are on a mission to build high quality platform to develop fast, flexible, easier to manage websites with Flextype!

If you wish to participate in the translation of Flextype, please [Join Flextype International Translator Team](https://flextype.org/en/international-translator-team) and start translating!

#### BUG REPORTING

We are using GitHub Issues to manage our public bugs. We keep a close eye on this so before filing a new issue, try to make sure the problem does not already exist.

#### PULL REQUESTS

We actively welcome your pull requests!

If you need help with Git or our workflow, please ask in our community chat. We want your contributions even if you're just learning Git. Our maintainers are happy to help!

#### DOCS

You may help us to create amazing knowledge base for Flextype. Fix spelling, add code examples, help organize, write new articles, and etc...


## SPONSORS

### Gold Sponsor

### Silver Sponsor

### Bronze Sponsor
<table>
  <tbody>
    <tr>
      <td align="center" valign="middle">
          <a href="https://web-easy.org">
              <img src="https://flextype.org/api/images/en/sponsors/webeasy.png?dpr=2&w=80&q=70&token=3b29b31ae05c89c2009f6e3f96e3d703" alt="" class="inline">
          </a>
      </td>
      <td align="center" valign="middle">
        <a href="#">
         Jeremy Monroe
        </a>
      </td>
    </tr>
  </tbody>
</table>

## LICENSE
[The MIT License (MIT)](https://github.com/flextype/flextype/blob/master/LICENSE.txt)
Copyright (c) 2018-2020 [Sergey Romanenko](https://github.com/Awilum)
