<p align="center">
    <img src="https://github.com/rawilum/rawilum/blob/dev/rawilum-logo-big.jpg?raw=true" alt="Rawilum" width="40%" height="40%" />
</p>

Rawilum is Modern Open Source Flat-File Content Management site.  
Content in Rawilum is just a simple files written with markdown syntax in pages folder.   
You simply create markdown files in the pages folder and that becomes a page.

## Requirements
PHP 7.1.3 or higher with PHP's [Multibyte String module](http://php.net/mbstring)   
Apache with [Mod Rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html)  

## Installation

#### Using (S)FTP

[Download the latest version.](http://rawilum.org/download)  

Unzip the contents to a new folder on your local computer, and upload to your webhost using the (S)FTP client of your choice. After you’ve done this, be sure to chmod the following directories (with containing files) to 777, so they are readable and writable by Rawilum:  
* `site/`

#### Using Composer

You can easily install Rawilum with Composer.

```
composer create-project rawilum/rawilum
```

## Contributing
1. Help on the [Forum.](http://forum.rawilum.org)
2. Develop a new plugin.
3. Create a new theme.
4. Find and [report issues.](https://github.com/rawilum/rawilum/issues)
5. Link back to [Rawilum](http://rawilum.org).

## Links
- [Site](http://rawilum.org)
- [Forum](http://forum.rawilum.org)
- [Documentation](http://rawilum.org/documentation)
- [Github Repository](https://github.com/rawilum/rawilum)

## License
See [LICENSE](https://github.com/rawilum/rawilum/blob/master/LICENSE.md)
