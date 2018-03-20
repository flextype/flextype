<p align="center">
    <img src="https://github.com/flextype/flextype/blob/dev/flextype-logo-big.jpg?raw=true" alt="Flextype" width="40%" height="40%" />
</p>

Flextype is Modern Open Source Flat-File Content Management site.  
Content in Flextype is just a simple files written with markdown syntax in pages folder.   
You simply create markdown files in the pages folder and that becomes a page.

## Requirements
PHP 7.1.3 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)  

## Installation

#### Using (S)FTP

[Download the latest version.](http://flextype.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 777, so they are readable and writable by Flextype:  
* `site/`

#### Using Composer

You can easily install Flextype with Composer.

```
composer create-project flextype/flextype
```

## Contributing
1. Help on the [Forum.](http://forum.flextype.org)
2. Develop a new plugin.
3. Create a new theme.
4. Find and [report issues.](https://github.com/flextype/flextype/issues)
5. Link back to [Flextype](http://flextype.org).

## Links
- [Site](http://flextype.org)
- [Forum](http://forum.flextype.org)
- [Documentation](http://flextype.org/documentation)
- [Github Repository](https://github.com/flextype/flextype)

## License
See [LICENSE](https://github.com/flextype/flextype/blob/master/LICENSE.md)
