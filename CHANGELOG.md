# Flextype 0.4.2, 2018-05-22
* Settings: cache.enabled is true from now
* Content: new methods added: initShortcodes() initMarkdown() markdown()
* Events: new events added: onMarkdownInitialized and onShortcodesInitialized

# Flextype 0.4.1, 2018-05-20
* Fixing issues with cache for getPages() method.
* Fixing issues with processPage() method.
* Fixing issues with all public methods in Cache class, from now all methods are static.
* Setting site.pages.flush_cache was removed from site.yaml file.

# Flextype 0.4.0, 2018-05-16
* Using SCSS for Simple theme
* Using Flextype Form Component
* Themes: class bug fixes
* Plugins: access for $locales changed to private
* Plugins: cache bug fixes
* New powerful Content class added for working with content instead of Pages, Shortcode, Markdown
* Content: new page field: `published` added
* Content: method for page blocks added
* Content: cache added for pages and blocks
* define CONSTANTS in PHP7 style
* Themes: changing from template() to view()

# Flextype 0.3.0, 2018-05-05
* Using Flextype Components instead of Symphony Components
* Available Flextype Components for developers: Arr, Assets, Cookie, Date, Debug, ErrorHandler, Event, Filesystem, Html, Http, I18n, Notification, Number, Registry, Session, Text, Token, View.
* Using `.yaml` files instead of `.yml`
* Default theme from now is `simple`
* Themes structure is changed. From now main folder for all theme templates and partials is `views` inside theme folder.
* Templates moved to `/simple/views/templates/` and partials moved to `/simple/views/partials/`
* Default template changes from `index.php` to `default.php`
* Plugins templates can be overridden by templates in themes folder.
* For pages we will not use `index.md` anymore. From now page file will have a name `page.md`
* Config class was removed, from now we will use simple powerful Registry Component to access configurations.
* Event, Filter, View class was removed. From now we are using more powerful Flextype Components!
* Fixed issue with getPages() method.
* Twitter Bootstrap updated to 4.1.0 for default theme.
* ErrorHandler added with errors logs.
* Code cleanup and refactoring #5

# Flextype 0.2.1, 2018-03-26
* date_format setting added to /site/config.site.yml
* Pages: Fixed bug with pages sort and slice in getPages() method
* Pages: Fixed bug with pages list for /pages folder
* Pages: Fixes for generating page url field
* Pages: Added ability to create date field automatically for pages if date field is not exists.
* Code cleanup and refactoring #5

# Flextype 0.2.0, 2018-03-23
* Thunderer Shortcode Framework - added
* Cache Flextype::VERSION for cache key - added
* flextype/boot/shortcodes.php	- removed
* flextype/boot/events.php - removed
* Code cleanup and refactoring #5

# Flextype 0.1.0, 2018-03-21
* Initial Release
