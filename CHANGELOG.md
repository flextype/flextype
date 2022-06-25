<a name="1.0.0-alpha.1"></a>
# [1.0.0-alpha.1](https://github.com/flextype/flextype/compare/v0.9.16...v1.0.0-alpha.1) (2022-XX-XX)

### Features

* **core** Core application updated from Slim 3 to Slim 4!

  See: [Upgrade Guide](https://www.slimframework.com/docs/v4/start/upgrade.html)

* **core** New PHP-DI added instead of Pimple DI.

  See: [Usage Guide](https://php-di.org/doc/frameworks/slim.html)

* **console** Added Extendable Flextype CLI Application.

    ```
    Flextype CLI Application 1.0.0-alpha.1

    Usage:
      command [options] [arguments]

    Options:
      -h, --help            Display help for the given command. When no command is given display help for the list command
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

    Available commands:
      completion                  Dump the shell completion script
      help                        Display help for a command
      list                        List commands
    cache
      cache:clear                 Clear cache.
      cache:clear-config          Clear cache config.
      cache:clear-data            Clear cache data.
      cache:clear-routes          Clear cache routes.
      cache:delete                Delete item.
      cache:delete-multiple       Delete mutiple items.
      cache:get                   Get item.
      cache:get-multiple          Get multiple items.
      cache:has                   Check whether cache item exists.
      cache:set                   Set item.
      cache:set-multiple          Set multiple items.
    entries
      entries:copy                Copy entry.
      entries:create              Create entry.
      entries:delete              Delete entry.
      entries:fetch               Fetch entry.
      entries:has                 Check whether entry exists.
      entries:move                Move entry.
      entries:update              Update entry.
    tokens
      tokens:create               Create a new unique token.
      tokens:delete               Delete token entry.
      tokens:fetch                Fetch token entry.
      tokens:generate             Generate token.
      tokens:generate-hash        Generate token hash.
      tokens:has                  Check whether token entry exists.
      tokens:update               Update tokens entry.
      tokens:verify-hash          Verify token hash.
    ```

* **core** Added `Thermage` library for better Flextype CLI Application styling.

* **core** Added new core constants: `PROJECT_NAME`, `PATH_PROJECT`, `PATH_TMP`. 

* **core** Added ability to run Flextype in silent mode by disabling `app` and `cli`. 

* **core** Added New [Glowy PHP](https://awilum.github.io/glowyphp/) Packages `View`, `Macroable`, `Strings`, `Arrays`, `Csrf`, `Filesystem`, `Registry`, `Session`.

* **core** Added built-in I18n module.

* **core** Added ability to override core defines.

* **entries** Added new functionality that allows implementing Virtual Entries by overriding default entries CRUD methods with the help of events.

* **entries** Added ability to create completely customisable high-level collections for entries with their fields and various formats.

* **entries** Added ability to set custom events for each entries collections.

* **entries** Added ability to create and use entries fields directives. 

  See: [Documentation](https://awilum.github.io/flextype/documentation/core/entries#directives)

* **entries** Added ability to create custom entries macros. 

  Built-in macros: `entries`, `php`, `registry`.

* **entries** Added new method `registry` to get entries registry.

* **entries** Added new method `options` to get entries options.

* **entries** Added new method `setOptions` to set entries options.

* **entries** Added new method `setRegistry` to set entries registry.

* **entries** Added ability to override logic for built-in custom fields.

    Example: 
    If you want to have your custom logic for processing field `uuid` just update flextype project settings.

    from:
    ```yaml
    ...
    entries:
      default:
        ...
        fields:
          ...
          uuid:
            enabled: true
            path: "src/flextype/core/Entries/Fields/Default/UuidField.php"
          ...
        ...
      ...
    ...
    ```

    to:
    ```yaml
    ...
    entries:
      default:
        ...
        fields:
          ...
          uuid:
            enabled: true
            path: "project/plugins/your-custom-plugin/Entries/Fields/Default/UuidField.php"
          ...
        ...
      ...
    ...
    ```

* **directives** Added new directive `@type` to set field type.

* **directives** Added new directive `@markdown` to parse markdown text inside current field.

* **directives** Added new directive `@shortcodes` to parse shortcodes text inside current field.

* **directives** Added new directive `@textile` to parse textile text inside current field.

* **directives** Added new directive `@php` to execute php code text inside current field.

* **directives** Added new directive `[[ ]]` to eval expression.

* **endpoints** All Rest API Endpoints codebase was rewritten from scratch.

* **endpoints** Added new Rest API Endpoint `POST /api/v1/cache/clear` to clear cache.

* **endpoints** Added new Rest API Endpoint `POST /api/v1/tokens/generate` to generate token hash.

* **endpoints** Added new Rest API Endpoint `POST /api/v1/tokens/verify-hash` to verify token hash.

* **endpoints** Added new Rest API Endpoint `POST /api/v1/tokens` to create token entry.

* **endpoints** Added new Rest API Endpoint `PATCH /api/v1/tokens` to update token entry.

* **endpoints** Added new Rest API Endpoint `DELETE /api/v1/tokens` to delete token entry.

* **endpoints** Added new Rest API Endpoint `GET /api/v1/tokens` to fetch token entry.

* **csrf** Added Glowy CSRF protection for Cross Site Request Forgery protection by comparing provided token with session token to ensure request validity.

* **frontmatter** Added ability to define custom frontmatter header parsers for entries. Example: instead of first `---` you may set serializer `---json`, `---json5` `---yaml` or `---neon`. 

* **serializers** Added new serializer `Json5`. 

  See: [Documentation](https://awilum.github.io/flextype/documentation/core/serializers/json5)

* **serializers** Added new serializer `Neon`. 

  See: [Documentation](https://awilum.github.io/flextype/documentation/core/serializers/neon)


* **serializers** Added ability to set global settings for all built-in serializers. 

    `/src/flextype/settings.yaml`
    ```yaml
    serializers:
      json: 
        decode:
          cache: 
            enabled: true
            string: ""
          assoc: true
          depth: 512
          flags: 0
        encode: 
          options: 0
          depth: 512
      json5: 
        decode:
          cache: 
            enabled: true
            string: ""
          assoc: true
          depth: 512
          flags: 0
        encode: 
          options: 0
          depth: 512
      yaml:
        decode:
          cache: 
            enabled: true
            string: ""
          native: true
          flags: 0
        encode:    
          inline: 10
          indent: 2
          flags: 0
      frontmatter:
        decode:
          cache: 
            enabled: true
            string: ""
          cache_id_string: ""
          header:
            serializer: yaml
            allowed: ['yaml', 'json', 'json5', 'neon']
        encode:    
          header:
            serializer: yaml
            allowed: ['yaml', 'json', 'json5', 'neon']
      neon:
        decode:
          cache: 
            enabled: true
            string: ""
        encode:
          blockMode: false
          indentation: "\t"
      phparray:
        decode:
          cache: 
            enabled: true
            string: ""
        encode:
          wrap: true
    ```

* **serializers** Added ability to set specific header serializer for `Frontmatter` serializer (default is YAML).

    `/src/flextype/settings.yaml`
    ```yaml
    frontmatter:
      decode:
        cache: 
          enabled: true
          string: ""
        cache_id_string: ""
        header:
          serializer: yaml
          allowed: ['yaml', 'json', 'json5', 'neon']
      encode:    
        header:
          serializer: yaml
          allowed: ['yaml', 'json', 'json5', 'neon']
    ```

* **parsers** Markdown parser [Commonmark updated to v2](https://commonmark.thephpleague.com/2.0/upgrading/)

* **parsers** Added new Textile parser.

* **parsers** Added ability to set global settings for all parsers. 

    `/src/flextype/settings.yaml`
    ```yaml
    parsers:
      markdown:
        cache: 
          enabled: true
          string: ""
        commonmark:
          renderer:
            block_separator: "\n"
            inner_separator: "\n"
            soft_break: "\n"
          commonmark:
            enable_em: true
            enable_strong: true
            use_asterisk: true
            use_underscore: true
            unordered_list_markers: ['-', '*', '+']
          html_input: 'allow'
          allow_unsafe_links: false
          max_nesting_level: 9223372036854775807
          slug_normalizer:
            max_length: 255
      textile:
        cache: 
          enabled: true
          string: ""
        restricted: false
        document_type: 'xhtml'
        document_root_directory: ''
        lite: false
        images: true
        link_relation_ship: ''
        raw_blocks: false
        block_tags: true
        line_wrap: true
        image_prefix: ''
        link_prefix: ''
        symbol: []
        dimensionless_images: true
      shortcodes:
        cache: 
          enabled: true
          string: ""
        cache_id_string: ""
        opening_tag: "("
        closing_tag: ")"
        closing_tag_marker: "/"
        parameter_value_separator: ":"
        parameter_value_delimiter: "'"
        shortcodes:
          entries:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/EntriesShortcode.php"
            fetch:
              enabled: true
          php:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/PhpShortcode.php"
          raw:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/RawShortcode.php"
          textile:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/TextileShortcode.php"
          markdown:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/MarkdownShortcode.php"
          registry:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/RegistryShortcode.php"
            get:
              enabled: true
          url:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/UrlShortcode.php"
          strings:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/StringsShortcode.php"
          filesystem:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/FilesystemShortcode.php"
            get:
              enabled: true
          i18n:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/I18nShortcode.php"
          if:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/IfShortcode.php"
          when:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/WhenShortcode.php"
          unless:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/UnlessShortcode.php"
          uuid:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/UuidShortcode.php"
          const:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/ConstShortcode.php"
          var:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/VarShortcode.php"
          field:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/FieldShortcode.php"  
          calc:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/CalcShortcode.php"  
          eval:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/EvalShortcode.php" 
          type:
            enabled: true
            path: "src/flextype/core/Parsers/Shortcodes/TypeShortcode.php" 
    ```

* **parsers** Added ability to override logic for built-in shortcodes.

    Example: 
    If you want to have your custom logic for processing shortcode `url` just update flextype settings.

    from:
    ```yaml
    ...
    parsers:
      shortcodes:
        shortcodes:
          ...
          url:
            enabled: true
            path: "/src/flextype/core/Parsers/Shortcodes/UrlShortcode.php"
          ...
        ...
      ...
    ...
    ```

    to:
    ```yaml
    ...
    parsers:
      shortcodes:
        shortcodes:
          ...
          url:
            enabled: true
            path: "/project/plugins/your-custom-plugin/Parsers/Shortcodes/UrlShortcode.php"
          ...
        ...
      ...
    ...
    ```

* **routes** Added ability to set custom projects routes in `/projects/routes/routes.php`.

* **shortcodes** Added new shortcode `(entries)` to fetch entry (or entries collection) or specific field.

* **shortcodes** Added new shortcode `(registry)` to fetch data from registry.

* **shortcodes** Added new shortcode `(filesystem)` to work with filesystem.

* **shortcodes** Added new shortcode `(uuid)` to generate uuid.

* **shortcodes** Added new shortcode `(strings)` for strings manipulation.

* **shortcodes** Added new shortcode `(textile)` to parse textile text.

* **shortcodes** Added new shortcode `(php)` to execute php code.

* **shortcodes** Added new shortcode `(eval)` to eval expression.

* **shortcodes** Added new shortcode `(calc)` to calculate values.

* **shortcodes** Added new shortcode `(type)` to set field type.

* **shortcodes** Added new shortcode `(markdown)` to parse markdown text.

* **shortcodes** Added new shortcode `(getBaseUrl)` to get base url.

* **shortcodes** Added new shortcode `(getBasePath)` to get base path.

* **shortcodes** Added new shortcode `(getAbsoluteUrl)` to get absolute url.

* **shortcodes** Added new shortcode `(urlFor)` to get url for route.

* **shortcodes** Added new shortcode `(getUriString)` to get uri string.

* **shortcodes** Added new shortcode `(filesystem)` to do filesytem manipulations.

* **shortcodes** Added new shortcode `(tr)` to returns translation of a string. 

* **shortcodes** Added new shortcode `(if)` to use logical if conditions.

* **shortcodes** Added new shortcode `(when)` to use logical positive if conditions.

* **shortcodes** Added new shortcode `(unless)` to use logical negative if conditions.

* **shortcodes** Added new shortcode `(var)` to get and set entry variables values.

* **shortcodes** Added new shortcode `(field)` to get entry fields values.

* **shortcodes** Added new shortcode `(const)` to get defined costants.

* **shortcodes** Added new shortcode `(raw)` to ignore shortcodes processing.

* **expressions** Added a new configurable and extendable expressions engine with a collection of predefined expressions.

* **expressions** Added new expression function `actions` to get actions service.

* **expressions** Added new expression function `collection` to create a new arrayable collection object from the given elements.

* **expressions** Added new expression function `collectionFromJson` to create a new arrayable collection object from the given JSON string.

* **expressions** Added new expression function `collectionFromString` to create a new arrayable collection object from the given string.

* **expressions** Added new expression function `collectionWithRange` to create a new arrayable object with a range of elements.

* **expressions** Added new expression function `collectionFromQueryString` to create a new arrayable object from the given query string.

* **expressions** Added new expression function `filterCollection` to filter collection.

* **expressions** Added new expression function `const` to get defined constants.

* **expressions** Added new expression function `var` to get current entry var.

* **expressions** Added new expression function `field` to get current entry field.

* **expressions** Added new expression function `csrf` to get csrf hidden input.

* **expressions** Added new expression function `entries` to get entries service.

* **expressions** Added new expression function `filesystem` to get filesystem instance.

* **expressions** Added new expression function `strings` to get strings instance.

* **expressions** Added new expression function `tr` to return translation of a string. If no translation exists, the original string will be returned.

* **expressions** Added new expression function `__` to return translation of a string. If no translation exists, the original string will be returned.

* **expressions** Added new expression function `parsers` to get parsers service.

* **expressions** Added new expression function `serializers` to get serializers service.

* **expressions** Added new expression function `registry` to get registry service.

* **expressions** Added new expression function `slugify` to get slugify service.

* **expressions** Added new expression function `urlFor` to get the url for a named route.

* **expressions** Added new expression function `fullUrlFor` to get the full url for a named route.

* **expressions** Added new expression function `isCurrentUrl` to determine is current url equal to route name.

* **expressions** Added new expression function `getCurrentUrl` to get current path on given Uri.

* **expressions** Added new expression function `getBasePath` to get the base path.

* **expressions** Added new expression function `getBaseUrl` to get the base url.

* **expressions** Added new expression function `getAbsoluteUrl` to get the absolute url.

* **expressions** Added new expression function `getProjectUrl` to get the project url.

* **expressions** Added new expression function `getUriString` to get the uri string.

* **expressions** Added new expression function `redirect` to get create redirect.

* **cache** Added new cache driver `Phparray` to store cache data in raw php arrays files.

* **cache** Added router cache.

* **cache** Added ability to set custom cache ID string for `entries`, `parsers` and `serializers`.

* **tokens** Added new Tokens API.

* **helpers** All core helpers are located in the `/src/flextype/helpers/`.

* **helpers** Added helper function `app` to get Flextype Application instance.

* **helpers** Added helper function `container` to get Flextype Application Container instance.

* **helpers** Added helper function `emitter` to get Flextype Emitter Service.

* **helpers** Added helper function `cache` to get Flextype Cache Service.

* **helpers** Added helper function `entries` to get Flextype Entries Service.

* **helpers** Added helper function `parsers` to get Flextype Parsers Service.

* **helpers** Added helper function `serializers` to get Flextype Serializers Service.

* **helpers** Added helper function `logger` to get Flextype Logger Service.

* **helpers** Added helper function `session` to get Flextype Session Service.

* **helpers** Added helper function `csrf` to get Flextype CSRF Service.

* **helpers** Added helper function `plugins` to get Flextype Plugins Service.

* **helpers** Added helper function `console` to get Flextype Console Service.

* **helpers** Added helper function `imageFile` to create a new image instance for image file.

* **helpers** Added helper function `imageCanvas` to create a new image canvas instance.

* **helpers** Added helper function `generateToken` to generate unique token.

* **helpers** Added helper function `generateTokenHash` to generate unique token hash.

* **helpers** Added helper function `verifyTokenHash` to validate token hash.

* **helpers** Added helper function `urlFor` to get url for a named route.

* **helpers** Added helper function `fullUrlFor` to get full url for a named route.

* **helpers** Added helper function `isCurrentUrl` to determine is current url equal to route name.

* **helpers** Added helper function `getCurrentUrl` to get current path on given Uri.

* **helpers** Added helper function `getBasePath` to get base path.

* **helpers** Added helper function `setBasePath` to set base path.

* **helpers** Added helper function `redirect` to create redirect.

* **helpers** Added helper function `upload` to upload files and process uloaded images.

* **uploder** Added Configurable Sirius Uploader for file upload. 

    ```yaml
    # Upload
    upload:

      # Uploads directory
      directory: '/uploads'

      # Overwrite existing files.
      overwrite: true

      # Auto-confirm uploads.
      autoconfirm: false

      # Prefixing uploads.
      prefix: ''

      # Validation options
      validation:

        # Allowed file extensions.
        allowed_file_extensions: ['gif', 'jpg', 'jpeg', 'png', 'ico', 'webm', 'svg']

        # Maximum file size.
        max_file_size: '24M'

        # Image validation options
        image:
        
          # Image maxiumum and minimum width
          width:
            max: 4920
            min: 100

          # Image maxiumum and minimum height
          height: 
            max: 3264
            min: 100
        
          # Image ratio
          #ratio:
          #  The option can be a number (eg: 1.3) or a ratio-like string (eg: 4:3, 16:9).
          #  size: 1.3

          #  The option error_margin specifies how much the image is allowed to 
          #  deviate from the target ratio. Default value is 0.
          #  error_margin: 0

      # Process uploaded files
      process:

        # Images process settings
        image:

          # Image quality
          quality: 70
    ```

* **macros** All core macros are located in the `/src/flextype/macros/`.

* **macros** Added `onlyFromCollection` and `exceptFromCollection` macros for Arrays ([#553](https://github.com/flextype/flextype/issues/553))

* **actions** Added new Actions API ([#549](https://github.com/flextype/flextype/issues/549))

    #### Usage
    
    ##### Example 1
    ```php
    // Set new action entries.create
    actions()->set('entries.create', function($id, $data) {
      return entries()->create($id, $data);
    });

    // Get action entries.create
    actions()->get('entries.create')('hello-world', []);
    ```

    ##### Example 2
    ```php
    // Set new action entries.update
    actions()->set('entries.update', function($id, $data) {
      if (entries()->update($id, $data)) {
        logger()->info("Content {$id} successfully updated");
        cache()->delete($id);
      } else {
        logger()->error("Content {$id} was not updated");
      }
    });

    // Get action entries.update
    actions()->get('entries.update')('hello-world', []);
    ```

    ##### Example 3
    ```php
    // Set new action entries.create
    actions()->set('entries.create', function($id, $data) {
      if(registry()->get('database') == 'MySQL') {
        // ... create new entry in the MySQL database.
      } else {
        return entries()->create($id, $data);
      }
    });

    // Get action entries.create
    actions()->get('entries.create')('blog/post-1', []);
    actions()->get('entries.create')('blog/post-2', []);
    actions()->get('entries.create')('blog/post-3', []);
    ```

    The Flextype Actions API provides new capabilities to extend the Flextype core by registering and reusing useful code snippets from global actions namespace.

### Bug Fixes

* **parsers** Fixed issue with double cashing.

* **htaccess** Security fixes for `.htaccess`

* **entries** Fixed issue when entries collection fetch returns empty result.

* **plugins** Fixed Plugins API translation loading process.

* **plugins** Fixed Plugins API initialization ([#551](https://github.com/flextype/flextype/issues/551))

* **plugins** Fixed Plugins API dependency initialization.

* **plugins** Fixed Plugins API issue with non valid plugins ([#551](https://github.com/flextype/flextype/issues/555))

* **plugins** Fixed Plugins API loader issue with disabled plugins.

### BREAKING CHANGES

* **media** Media and Glide functionality removed from the flextype core due to security and perfomance reasons. Recomended to use imagekit or imgix instead.

* **core** Use new constant `PATH_PROJECT` instead of `PATH['project']` and `PATH_TMP` instead of `PATH['tmp']`.

* **shortcodes** New default shortcodes syntax and signatures changes for all shortocodes.
  ```yaml
    opening_tag: "("
    closing_tag: ")"
    closing_tag_marker: "/"
    parameter_value_separator: ":"
    parameter_value_delimiter: '"'
  ```

* **helpers** Use new helpers functions to access Flextype Services.

  * use `entries()` instead of `flextype('entries')`
  * use `session()` instead of `flextype('session')`
  * use `cache()` instead of `flextype('cache')`
  * use `app()` instead of `flextype()`
  * use `container()` instead of `flextype('container_name_here')`
  * use `parsers()` instead of `flextype('parsers')`
  * use `serializers()` instead of `flextype('serializers')`
  * use `plugins()` instead of `flextype('plugins')`
  * use `emitter()` instead of `flextype('emitter')`
  * use `logger()` instead of `flextype('logger')`
  * use `registry()` instead of `flextype('registry')`

* **helpers** Use helper function `app` to access Flextype Application instance instead of old helper function `flextype()`.

* **helpers** Use helper function `container` to access Flextype Application container instead of old helper function `flextype()` with container name argument.

  * use `container()->get('entries')` instead of `flextype('entries')`
  * use `container()->set('entries', new Entries())` instead of `flextype()['entries'] = new Entries()`

* **helpers** Use helper function `filterCollection` instead of old `filter`.

* **tokens** Project tokens moved from `/project/tokens/` to `/project/entries/tokens/`.

* **entries** Changes for etnries memory storage.

  * use `entries()->registry()->get()` instead of `flextype('entries')->storage()->get()`
  * use `entries()->registry()->set()` instead of `flextype('entries')->storage()->set()`
  * use `entries()->registry()->has()` instead of `flextype('entries')->storage()->has()`
  * use `entries()->registry()->delete()` instead of `flextype('entries')->storage()->delete()`

  note: all method from Atomastic Arrays are available for Arrays Storage Object manipulations
  docs: https://github.com/atomastic/arrays

### Refactoring

* **core** General code refactoring and improvements.

* **tests** All unit tests were rewritten.

<a name="0.9.16"></a>
# [0.9.16](https://github.com/flextype/flextype/compare/v0.9.15...v0.9.16) (2021-01-14)

### Features

* **parsers** Added commonmark instead of parsedown ([#540](https://github.com/flextype/flextype/issues/540))

  See: [Documentation](https://awilum.github.io/flextype/documentation/core/parsers/markdown)

* **shortcodes** Added new shortcode - media_files_fetch

    example:

    ```
    [media_files_fetch id="entries/home/foo.txt" field="title" default="Bar"]
    ```

### Bug Fixes

* **bootstrap** Fixed include path for dependencies.

### Refactoring

* **core** general code refactoring and improvements.

<a name="0.9.15"></a>
# [0.9.15](https://github.com/flextype/flextype/compare/v0.9.14...v0.9.15) (2021-01-03)

### Features

* **media** Added method `has()` for Media Folders ([#534](https://github.com/flextype/flextype/issues/534))
* **entries** simplify functionality to work with online entries storage. ([#536](https://github.com/flextype/flextype/issues/536))
* **parsers** move markdown and shortcode settings under parsers setting. ([#539](https://github.com/flextype/flextype/issues/539))

### Bug Fixes

* **entries** Fixed issue with individual entries cache field ([#537](https://github.com/flextype/flextype/issues/537))
* **plugins** Fixed issue with empty manifest and settings yaml files ([#538](https://github.com/flextype/flextype/issues/538))

### BREAKING CHANGES

* **entries** according to this ticket ([#536](https://github.com/flextype/flextype/issues/536)) we have several changes for entries storage.

    * use `flextype('entries')->storage()->get()` instead of `flextype('entries')->getStorage()`
    * use `flextype('entries')->storage()->set()` instead of `flextype('entries')->setStorage()`
    * use `flextype('entries')->storage()->has()` instead of `flextype('entries')->hasStorage()`
    * use `flextype('entries')->storage()->delete()` instead of `flextype('entries')->deleteStorage()`

    note: all method from Atomastic Arrays are available for Arrays Storage Object manipulations
    docs: https://github.com/atomastic/arrays


<a name="0.9.14"></a>
# [0.9.14](https://github.com/flextype/flextype/compare/v0.9.13...v0.9.14) (2020-12-30)

### Features

* **core** Moving to PHP 7.4.0 ([#524](https://github.com/flextype/flextype/issues/524))

* **plugins** Set default plugin priority 100 and SORT them ascending ([#523](https://github.com/flextype/flextype/issues/523))

### Bug Fixes

* **core** Fixed issue with Rest API endpoints detection. ([#522](https://github.com/flextype/flextype/issues/522))

* **entries** Fixed issue with empty variable $data in fetch() method. ([#531](https://github.com/flextype/flextype/issues/531))

* **entries** Fixed issue with deleteStorage() method return data.

### Refactoring

* **core** general code refactoring and improvements.

<a name="0.9.13"></a>
# [0.9.13](https://github.com/flextype/flextype/compare/v0.9.12...v0.9.13) (2020-12-20)

### Features

* **media-files** we will use `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#508](https://github.com/flextype/flextype/issues/508))

    ```php
    /**
     * Fetch.
     *
     * @param string $id      The path to file.
     * @param array  $options Options array.
     *
     * @access public
     *
     * @return self Returns instance of The Arrays class.
     */
    public function fetch(string $id, array $options = []): Arrays
    ```

    Media Files API is macroable and we will able to Added any custom fetch methods for receiving data from different sources.

    ```php
    flextype('media')->files()::macro('fetchFromOtherStorage', function(string $id, array $options) {
      // fetch data from Other Storage using $id and $options
    });

    $data = flextype('media')->files()-> fetchFromOtherStorage($id, $options);
    ```

* **media-folders** we will use `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#509](https://github.com/flextype/flextype/issues/509))

    ```php
    /**
     * Fetch.
     *
     * @param string $id      The path to folder.
     * @param array  $options Options array.
     *
     * @access public
     *
     * @return self Returns instance of The Arrays class.
     */
    public function fetch(string $id, array $options = []): Arrays
    ```

    Media Folders API is macroable and we will able to Added any custom fetch methods for receiving data from different sources.

    ```php
    flextype('media')->folders()::macro('fetchFromOtherStorage', function(string $id, array $options) {
      // fetch data from Other Storage using $id and $options
    });

    $data = flextype('media')->folders()-> fetchFromOtherStorage($id, $options);
    ```

* **entries** we will use `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#495](https://github.com/flextype/flextype/issues/495))

    ```php
    /**
    * Fetch.
    *
    * @param string $id      Unique identifier of the entry.
    * @param array  $options Options array.
    *
    * @access public
    *
    * @return mixed
    */
    public function fetch(string $id, array $options = []): Arrays
    ```

    Entries API is macroable and we will able to Added any custom fetch methods for receiving data from different sources.

    ```php
    flextype('entries')::macro('fetchXML', function(string $id, array $options) {
    // fetch data from XML using $id and $options
    });

    $data = flextype('entries')->fetchXML($id, $options);
    ```

* **images** we will use `media/` folder instead of `uploads/entries/` ([#516](https://github.com/flextype/flextype/issues/516))

* **serializers** standardise serializers container names with macroable ability. ([#518](https://github.com/flextype/flextype/issues/518))

    **New methods to access Serializers:**  

    ```php
    flextype('serializers')->yaml()
    flextype('serializers')->json()
    flextype('serializers')->frontmatter()
    ```

    **Adding macros:**  

    ```php
    flextype('serializers')::macro('NAME', CALLBACK_FUNCTION() {});
    ```

* **parsers** standardise parsers container names with macroable ability. ([#519](https://github.com/flextype/flextype/issues/519))

    **New methods to access Parsers:**  

    ```php
    flextype('parsers')->shortcode()
    flextype('parsers')->markdown()
    ```

    **Adding macros:**  

    ```php
    flextype('parsers')::macro('NAME', CALLBACK_FUNCTION() {});
    ```

* **media** standardise media container names with macroable ability for Media API. ([#517](https://github.com/flextype/flextype/issues/517))

    New macroable common class for all media - `class Media`  

    **New methods to access Media API:**  

    ```php
    flextype('media')->files()
    flextype('media')->files()->meta()
    flextype('media')->folders()
    flextype('media')->folders()->meta()
    ```

    **Adding macros:**  

    ```php
    flextype('media')::macro('NAME', CALLBACK_FUNCTION() {});
    flextype('media')->files()::macro('NAME', CALLBACK_FUNCTION() {});
    flextype('media')->files()->meta()::macro('NAME', CALLBACK_FUNCTION() {});
    flextype('media')->folders()::macro('NAME', CALLBACK_FUNCTION() {});
    flextype('media')->folders()->meta()::macro('NAME', CALLBACK_FUNCTION() {});
    ```

* **fields** Added new field `registry.get` for Registry API ([#494](https://github.com/flextype/flextype/issues/494))

    Registry API provides method `get()` for retrieving data from registry and we should able to access them inside entries frontmatter header for retrieving data right in the entries.

    **Basic Example**

    Sample entry with several queries and with several nested queries inside of children entries.

    File: `/project/entries/registry-root/entry.md`

    ```yaml
    ---
    title: Root
    registry:
      get:
        flextype:
          key: flextype.manifest.name
        author.name:
          key: flextype.manifest.author.name
        license:
          key: flextype.manifest.license
    entries:
      fetch:
        level1:
          id: registry-root/level-1
    ---
    ```

    **Setting for this fields**

    File: `/project/config/flextype/settings.yaml`

    ```yaml
    entries:
      fields:
        registry:
          get:
            enabled: true
    ```

    Valid values for setting **enabled** is **true** or **false**

* **fields** Added new field `entries.fetch` for Entries API ([#492](https://github.com/flextype/flextype/issues/492))

    Entries API provides methods for entries fetch: `fetch()` and we should able to access them inside entries frontmatter header for fetching data right in the entries. Also, we will able to Added and use any kind of fetch methods with our macroable functionality.  

    **Basic Example**  

    Catalog entry with several queries and with several nested queries inside of children entries.

    File: `/project/entries/catalog/entry.md`

    ```yaml
    ---
    title: Catalog
    visibility: visible
    entries:
      fetch:
        label1:
          id: discounts/50-off
          options:
            filter:
              limit: 4
        bikes:
          id: catalog/bikes
          options:
            collection: true
            filter:
              where:
                -
                  key: brand
                  operator: eq
                  value: gt
              limit: 10
        discounts:
          id: discounts
          options:
            collection: true
            filter:
              where:
                -
                  key: title
                  operator: eq
                  value: '30% off'
                -
                  key: category
                  operator: eq
                  value: bikes
    ---
    ```

    **Setting for this fields**  

    File: `/project/config/flextype/settings.yaml`

    ```yaml
    entries:
      fields:
        entries:
          fetch:
            enabled: true
            result: toObject
    ```

    Valid values for setting **enabled** is **true** or **false**  
    Valid values for setting **result** is **toObject** or **toArray**  

* **fields** Added new field `media.files.fetch` and `media.folders.fetch` for Media API's ([#501](https://github.com/flextype/flextype/issues/501)) ([#500](https://github.com/flextype/flextype/issues/500))

    Media API's provides methods for files and folders fetch: `fetch()` and we should able to access them inside entries frontmatter header for fetching data right in the entries. Also, we will able to Added and use any kind of fetch methods with our macroable functionality.

    ```yaml
    ---
    title: Media
    media:
      folders:
        fetch:
          macroable_folder:
            id: 'foo'
            options:
              method: fetchExtraData
          foo_folder:
            id: 'foo'
          collection_of_folders:
            id: '/'
            options:
              collection: true
      files:
        fetch:
          macroable_file:
            id: 'foo'
            options:
              method: fetchExtraData
          foo_file:
            id: foo.txt
          collection_of_files:
            id: '/'
            options:
              collection: true
    ---
    ```

    **Setting for this fields**  

    File: `/project/config/flextype/settings.yaml`

    ```yaml
    entries:
      fields:
        media:
          files:
            fetch:
              enabled: true
              result: toObject
          folders:
            fetch:
              enabled: true
              result: toObject
    ```

    Valid values for setting **enabled** is **true** or **false**    
    Valid values for setting **result** is **toObject** or **toArray**   


* **entries** Added new method `deleteStorage()` for Entries API ([#498](https://github.com/flextype/flextype/issues/498))

* **entries** Added new method `hasStorage()` for Entries API ([#497](https://github.com/flextype/flextype/issues/497))

* **core** Added new method `isApiRequest` to Determine API Request in the basic core functionality. ([#507](https://github.com/flextype/flextype/issues/507))

* **rest-api-entries** Added ability to send options for `fetch()` methods in Entries Rest API. ([#504](https://github.com/flextype/flextype/issues/504))

    **Fetch single**
    ```
    GET /api/entries?id=YOUR_ENTRY_ID&token=YOUR_ENTRIES_TOKEN
    ```

    **Fetch single with options**
    ```
    GET /api/entries?id=YOUR_ENTRY_ID&options=[filter]&token=YOUR_ENTRIES_TOKEN
    ```

    **Fetch collection**
    ```
    GET /api/entries?id=YOUR_ENTRY_ID&options[collection]=true&token=YOUR_ENTRIES_TOKEN
    ```

    **Fetch collection with options**
    ```
    GET /api/entries?id=YOUR_ENTRY_ID&options[collection]=true&options=[find]&[filter]&token=YOUR_ENTRIES_TOKEN
    ```

* **rest-api-entries** Added ability to call macroable fetch methods. ([#505](https://github.com/flextype/flextype/issues/505))

    With help of query option `?options[method]=` we should able to call any macroable fetch methods.

    ### Example

    **Macroable method XML**

    ```
    flextype('entries')::macro('fetchXml', function(string $id, array $options) {
      return ['XML DATA HERE'];
    });
    ```

    **HTTP GET:**

    ```
    GET /api/entries?id=YOUR_ID&options[method]=fetchXml&token=YOUR_ENTRIES_TOKEN
    ```

* **rest-api-media** reorganize endpoints for Media Rest API ([#514](https://github.com/flextype/flextype/issues/514))

* **rest-api-media** Added ability to call macroable fetch methods for Folder. ([#512](https://github.com/flextype/flextype/issues/512))

    With help of query option `?options[method]=` we should able to call any macroable fetch methods.

    ### Example

    **Macroable method**

    ```
    flextype('media')->folders()::macro('fetchFromOtherStorage', function(string $id, array $options) {
      // fetch data from Other Storage using $id and $options
    });
    ```

    **HTTP GET:**

    ```
    GET /api/folders?id=YOUR_MEDIA_FILES_ID&options[method]= fetchFromOtherStorage&token=YOUR_MEDIA_FOLDERS_TOKEN
    ```

* **rest-api-media** Added ability to call macroable fetch methods for Files. ([#513](https://github.com/flextype/flextype/issues/513))

    With help of query option `?option[method]=` we should able to call any macroable fetch methods.

    ### Example

    **Macroable method**

    ```
    flextype('media')->files()::macro('fetchFromOtherStorage', function(string $id, array $options) {
      // fetch data from Other Storage using $id and $options
    });
    ```

    **HTTP GET:**

    ```
    GET /api/files?id=YOUR_MEDIA_FILES_ID&option[method]=fetchFromOtherStorage&token=YOUR_MEDIA_FILES_TOKEN
    ```

### Bug Fixes

* **fields** Fixed issue with slug field in Entries API ([#520](https://github.com/flextype/flextype/issues/520))

* **core** Fixed issue with invalid timezone setting ([#490](https://github.com/flextype/flextype/issues/490))

* **entries** Fixed issue with not exists entries collections. ([#503](https://github.com/flextype/flextype/issues/503))

* **entries** Fixed issue with collisions in Entries API $storage for entries fetching. ([#496](https://github.com/flextype/flextype/issues/496))

* **rest-api-entries** Fixed issue with 404 status code in Entries Rest API ([#502](https://github.com/flextype/flextype/issues/502))

* **rest-api** Fixed issue with Rest API endpoints initialisation. ([#506](https://github.com/flextype/flextype/issues/506))

### BREAKING CHANGES

* **media** standardise media container names with macroable ability for Media API. ([#517](https://github.com/flextype/flextype/issues/517))

    | NEW CONTAINER           | OLD CONTAINER              |   
    |------------------|------------------|
    | media      | media_files, media_files_meta, media_folders, media_folders_meta      |

* **parsers** standardise parsers container names with macroable ability. ([#519](https://github.com/flextype/flextype/issues/519))

    | NEW CONTAINER           | OLD CONTAINER              |   
    |------------------|------------------|
    | parsers      | shortcode, markdown       |

* **serializers** standardise serializers container names with macroable ability. ([#518](https://github.com/flextype/flextype/issues/518))

    | NEW CONTAINER           | OLD CONTAINER              |   
    |------------------|------------------|
    | serializers      | yaml, json, frontmatter       |

* **rest-api-media** reorganize endpoints for Media Rest API ([#514](https://github.com/flextype/flextype/issues/514))

    |  | NEW ENDPOINT | OLD ENDPOINT |
    |---|---|---|
    | **GET** |  /api/media/files |  /api/files |
    | **POST** |  /api/media/files |  /api/files |
    | **PUT** |  /api/media/files |  /api/files |
    | **PATCH** |  /api/media/files |  /api/files |
    | **DELETE** |  /api/media/files |  /api/files |
    | **POST** |  /api/media/files/copy |  /api/files/copy |
    | **PATCH** |  /api/media/files/meta |  /api/files/meta |
    | **POST** |  /api/media/files/meta |  /api/files/meta |
    | **DELETE** |  /api/media/files/meta |  /api/files/meta |
    | **GET** |  /api/media/folders |  /api/folders |
    | **POST** |  /api/media/folders |  /api/folders |
    | **PATCH** |  /api/media/folders |  /api/folders |
    | **DELETE** |  /api/media/folders |  /api/folders |
    | **POST** |  /api/media/folders/copy |  /api/folders/copy |

    **Tokens:**
    - token for files should be moved from `/tokens/files/` to `/tokens/media/files/`
    - token for folders should be moved from `/tokens/folders/` to `/tokens/media/folders/`

    **Settings:**
    ```yaml
    api:
      ...
      media:
        files:
          enabled: true
          default_token:
        folders:
          enabled: true
          default_token:
    ```

* **helpers** `filter` helper return `array` result every time and not `int` or `bool`. ([#493](https://github.com/flextype/flextype/issues/493))

* **helpers** `filter` helper not support `slice_offset` and `slice_limit` because they are are duplicates already exists functionality `offset` and `limit`. ([#493](https://github.com/flextype/flextype/issues/493))

* **helpers** `filter` helper not support `count` and `exists`. ([#493](https://github.com/flextype/flextype/issues/493))

* **entries** we have changes in the events names for Entries API ([#499](https://github.com/flextype/flextype/issues/499))

    Events:

    **onEntriesFetch**  
    **onEntriesFetchSingle** instead of **onEntryInitialized**  
    **onEntriesFetchSingleCacheHasResult** instead of **onEntryAfterCacheInitialized**  
    **onEntriesFetchSingleNoResult**  
    **onEntriesFetchSingleHasResult** instead of **onEntryAfterInitialized**  
    **onEntriesFetchCollection** instead of **onEntriesInitialized**  
    **onEntriesFetchCollectionHasResult** instead of **onEntriesAfterInitialized**  
    **onEntriesFetchCollectionNoResult**  
    **onEntriesMove** instead of **onEntryMove**  
    **onEntriesUpdate** instead of **onEntryUpdate**  
    **onEntriesCreate** instead of **onEntryCreate**  
    **onEntriesDelete** instead of **onEntryDelete**  
    **onEntriesCopy** instead of **onEntryCopy**  
    **onEntriesHas** instead of **onEntryHas**  

* **entries** Flextype EMS structure is changes because of issues with collisions ([#496](https://github.com/flextype/flextype/issues/496))

    Updated structure:

    ```php
    $storage = [
        'fetch' => [
          'id' => '',
          'data' => [],
          'options' => [
              'find' => [],
              'filter' => [],
          ],
        ],
        'create' => [
          'id' => '',
          'data' => [],
        ],
        'Updated' => [
          'id' => '',
          'data' => [],
        ],
        'delete' => [
          'id' => '',
        ],
        'copy' => [
          'id' => '',
          'newID' => '',
        ],
        'move' => [
          'id' => '',
          'newID' => '',
        ],
        'has' => [
          'id' => '',
        ],
    ];
    ```
* **rest-api-entries** Entries Rest API - for collection fetch we should define this in the request query `&options[collection]=true`

* **rest-api-entries** Entries Rest API - instead of `&filter=[]` we should define filtering in the request query like this `&options[find]` and `&options[filter]`

* **rest-api-media-files** Media Files Rest API - for collection fetch we should define this in the request query `&options[collection]=true`

* **rest-api-media-folders** Media Folders Rest API - instead of `&filter=[]` we should define filtering in the request query like this `&options[find]` and `&options[filter]`

* **images** we will use `media/` folder instead of `uploads/entries/` ([#516](https://github.com/flextype/flextype/issues/516))

    - folder `uploads/entries/` should should be renamed to `media/entries/` related to this ticket: #515
    - in the endpoint `/api/images/{path:.+}` path for entries, should starts with `/entries/`.

    **Example:**  

    **old:** `/api/images/home/banner.jpg`
    **new:** `/api/images/entries/home/banner.jpg`

* **entries** we should use only `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#495](https://github.com/flextype/flextype/issues/495))

    - method `fetchSingle()` removed. Use `fetch($id, $options)` method.
    - methods `fetchCollection` removed. Use  `fetch($id, ['collection' => true])` method.

* **media-folders** we should use only `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#509](https://github.com/flextype/flextype/issues/509))

    - method `fetchSingle()` removed. Use `fetch($id, $options)` method.
    - methods `fetchCollection` removed. Use  `fetch($id, ['collection' => true])` method.

* **media-files** we should use only `fetch()` method as entry point to execute different methods with `fetch` prefix. ([#508](https://github.com/flextype/flextype/issues/508))

    - method `fetchSingle()` removed. Use `fetch($id, $options)` method.
    - methods `fetchCollection` removed. Use  `fetch($id, ['collection' => true])` method.

<a name="0.9.12"></a>
# [0.9.12](https://github.com/flextype/flextype/compare/v0.9.11...v0.9.12) (2020-12-07)

### Features
* **core** Added Atomastic Components instead of Flextype Components ([#478](https://github.com/flextype/flextype/issues/478))

    Added:
    - atomastic/session
    - atomastic/arrays
    - atomastic/filesystem
    - atomastic/registry
    - atomastic/strings

* **entries** Entries API return Arrays Object instead of plain array on fetch. ([#485](https://github.com/flextype/flextype/issues/485))

    From no we have ability to work with entries singles and collections as with smart objects
    for further data manipulations with help of Atomastic Arrays Component.

    Example:
    ```php
    // Fetch random 10 posts created by Awilum and sort them by published_at field.
    $posts = flextype('entries')
               ->fetchCollection('blog')
               ->where('author.name', 'eq', 'Awilum')
               ->sortBy('published_at')
               ->limit(10)
               ->random();
    ```

* **entries** Standardize Entries API fetch. ([#486](https://github.com/flextype/flextype/issues/486))

* **entries** Standardize Media Files API fetch. ([#487](https://github.com/flextype/flextype/issues/487))

* **entries** Standardize Media Folders API fetch. ([#488](https://github.com/flextype/flextype/issues/488))

* **entries** Added ability to extend Core class with Macros. ([#489](https://github.com/flextype/flextype/issues/489))

* **cache** Added new cache engine - PHPFastCache instead of Doctrine Cache ([#457](https://github.com/flextype/flextype/issues/457))

    #### New config for PhpFastCache
    https://github.com/flextype/flextype/blob/dev/src/flextype/settings.yaml#L127-L241  

    #### New methods from PhpFastCache
    We are start using PhpFastCache PSR16 adapter  
    https://github.com/PHPSocialNetwork/phpfastcache   


* **core** Unit Test powered by PestPHP.

* **media** Added new `move()` method instead of `rename()`

* **entries** Added new `move()` method instead of `rename()`

* **core** Added new `PATH_TMP` constant ([#470](https://github.com/flextype/flextype/issues/470))

    Now we have:

    `PATH_TMP` constant instead of `PATH['cache']` and `PATH['logs']`

* **markdown** Added markdown basic settings ([#471](https://github.com/flextype/flextype/issues/471))

    ```yaml
    markdown:
      auto_line_breaks: false
      auto_url_links: false
      escape_markup: false
    ```

* **markdown** Added ability to access markdown parser instance ([#468](https://github.com/flextype/flextype/issues/468))

    Usage:

    ```php
    $markdown = flextype('markdown')->getInstance();
    ```

* **entries** Added new Flextype Entries Memory Storage (Flextype EMS). New private property `$storage` for storing current requested entry(or entries) data and all Entries CRUD operations data in memory with ability to change them dynamically on fly. New public methods `getStorage()` `setStorage()` ([#467](https://github.com/flextype/flextype/issues/467))

    Structure (Flextype EMS):

    ```php
    $storage = [
        'fetch' => [
          'id' => '',
          'data' => '',
        ],
        'create' => [
          'id' => '',
          'data' => '',
        ],
        'Updated' => [
          'id' => '',
          'data' => '',
        ],
        'delete' => [
          'id' => '',
        ],
        'copy' => [
          'id' => '',
          'new_id' => '',
        ],
        'move' => [
          'id' => '',
          'new_id' => '',
        ],
        'has' => [
          'id' => '',
        ],
    ];
    ```

    Accessing storage example:

    ```php
    flextype('emitter')->AddedListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->setStorage('fetch.data.title', 'New title');
    });

    $entry = flextype('entries')->fetchSingle('about');

    echo $entry['title'];
    ```

* **entries** Added new events: `onEntryHas`, `onEntryInitialized`, `onEntriesInitialized` ([#467](https://github.com/flextype/flextype/issues/467))

* **helpers** Added new support helper `find()` for files and directories searching instead of `find_filter()`

* **helpers** Added new support helper `filter()` for data collection filtering instead of `arrays_filter()`

### Bug Fixes

* **entries** Fixed issue with `delete()` method ([#465](https://github.com/flextype/flextype/issues/465))

* **media** Fixed issue with `exif_read_data()` on files upload.

### Refactoring

* **entries** Removed App from all core namespaces ([#469](https://github.com/flextype/flextype/issues/469))

### BREAKING CHANGES

* **entries** removed properties from Entries API ([#467](https://github.com/flextype/flextype/issues/467))

    ```php  
    $entry_id
    $entry
    $entry_create_data
    $entry_update_data
    $entries_id
    $entries
    ```

    Use public methods `getStorage()` `setStorage()` instead.

    Example:

    ```php
    // old
    flextype('entries')->entry['title'] = 'New title';

    // new
    flextype('entries')->setStorage('fetch.data.title', 'New title');

    // old
    $title = flextype('entries')->entry['title'];

    // new
    $title = flextype('entries')->getStorage('fetch.data.title');
    $title = flextype('entries')->getStorage('fetch.data')['title'];
    ```
* **core** Removed App from all core namespaces ([#469](https://github.com/flextype/flextype/issues/469))

    **We should have**

    ```
    use Flextype\Foundation\Entries\Entries;
    ```

    **instead of**

    ```
    use Flextype\App\Foundation\Entries\Entries;
    ```

* **core** use new `PATH_TMP` constant instead of `PATH['cache']` and `PATH['logs']` ([#470](https://github.com/flextype/flextype/issues/470))

* **cache** old cache config removed, use new config for PhpFastCache ([#457](https://github.com/flextype/flextype/issues/457))

* **cache** use methods `has()` `set()` `get()` instead of `contains()` `save()` `fetch()` ([#457](https://github.com/flextype/flextype/issues/457))

* **core** Removed flextype-components/session ([#473](https://github.com/flextype/flextype/issues/473))

* **core** Removed flextype-components/cookie ([#473](https://github.com/flextype/flextype/issues/473))

* **core** Removed flextype-components/number ([#474](https://github.com/flextype/flextype/issues/474))

* **core** Removed flextype-components/filesystem ([#474](https://github.com/flextype/flextype/issues/474))

* **core** Removed flextype-components/arrays ([#474](https://github.com/flextype/flextype/issues/474))


<a name="0.9.11"></a>
# [0.9.11](https://github.com/flextype/flextype/compare/v0.9.10...v0.9.11) (2020-08-25)

### Features

* New helper function Addeded for access all Flextype features in one place

    ```php
    flextype($container_name = null, $container = [])
    ```

    **IMPORTANT**

    Do not use `$flextype` object to access Flextype features, use `flextype()` helper function.

### Bug Fixes

* **core** Fixed bug - Cannot access protected property Flextype\App\Foundation\Flextype::$container ([#462](https://github.com/flextype/flextype/issues/462))
* **core** Fixed bug - Cannot use object of type Flextype\App\Foundation\Flextype as array ([#461](https://github.com/flextype/flextype/issues/461))
* **media** Fixed Media exif_read_data warning - File not supported ([#464](https://github.com/flextype/flextype/issues/464))

### Refactoring

* **plugins** Removed $flextype variable from plugins init method.
* **entries** Updated return type for fetch() method.
* **entries** Added Addeditional check for getTimestamp() method in the getCacheID()
* **entries** Removed dead code from fetchCollection() method.

### Vendor Updates

* **core** Updated vendor flextype-components/filesystem to 2.0.8
* **core** Updated vendor ramsey/uuid to 4.1.1

<a name="0.9.10"></a>
# [0.9.10](https://github.com/flextype/flextype/compare/v0.9.9...v0.9.10) (2020-08-19)

### Features

* **core** Moving to PHP 7.3.0 ([#456](https://github.com/flextype/flextype/issues/456))
* **core** Added new class `Flextype` that extends `Slim\App` ([#458](https://github.com/flextype/flextype/issues/458))

    with methods:

    ```
    /**
     * Get Dependency Injection Container.
     *
     * @param string $key DI Container key.
     */
    public function container(?string $key = null)

    /**
     * Returns Flextype Instance
     */
    public static function getInstance()

    /**
     * This method will returns the current Flextype version
     */
    public static function getVersion() : string
    ```

* **collection** Added `only()` method for Collection ([#455](https://github.com/flextype/flextype/issues/455))

    Example:
    ```
    ...->only(['id', 'title'])->...
    ```

* **entries** Rename path to id in Entries API ([#453](https://github.com/flextype/flextype/issues/453))

    New implementation
    ```
    // Entry properties
    $entry_id
    $entries_id

    // Arguments
    $id
    $new_id
    ```

* **shortcode** Added New Shortcode ([#454](https://github.com/flextype/flextype/issues/454))

    ```
    [raw] Raw shortcode content [/raw]
    ```

* **shortcode** Added New Shortcode Methods ([#454](https://github.com/flextype/flextype/issues/454))

    ```
    // Get shortcode instance.
    getInstance()

    // Added shortcode handler.
    AddedHandler(string $name, $handler)

    // Added event handler.
    AddedEventHandler($name, $handler)

    // Processes text and replaces shortcodes.
    process(string $input, bool $cache = true)
    ```

### Bug Fixes

* **entries** Fixed issue with entries paths on Windows ([#460](https://github.com/flextype/flextype/issues/460))
* **cache** Fixed issue with `purge()` method. ([#451](https://github.com/flextype/flextype/issues/451))
* **entries** Fixed wrong Implementation of Slug Field for Entries ([#452](https://github.com/flextype/flextype/issues/452))
* **entries** Added new entry field `id` ([#452](https://github.com/flextype/flextype/issues/452))

### BREAKING CHANGES

* **entries** Rename path to id in Entries API ([#453](https://github.com/flextype/flextype/issues/453))

    Old Entry properties
    ```
    $entry_path
    $entries_path
    ```

    New Entry properties
    ```
    $entry_id
    $entries_id
    ```

* **entries** Fixed wrong Implementation of Slug Field for Entries ([#452](https://github.com/flextype/flextype/issues/452))

    From now we have entry fields:

    `slug` with current entry slug.

    Example:
    ```
    flextype-0.9.10
    ```

    `id` with current entry full path as it is was for slug field.

    Example:
    ```
    blog/flextype-0.9.10
    ```

* **shortcode** We should use `process()` method instead of `parse()` for shortcode processing. ([#454](https://github.com/flextype/flextype/issues/454))

    Example of new usage in PHP:

    ```
    ...->shortcode->process($input, $cache);
    ```

* **core** `$container`, `$flextype` and `$app` objects removed! ([#458](https://github.com/flextype/flextype/issues/458))

    We should use new object `$flextype` as it is a consolidate entry point to all Flextype features.

    Here is some examples:

    ```
    // OLD
    $app->get(...)
    $app->post(...)
    ...

    // NEW
    $flextype->get(...)
    $flextype->post(...)
    ...
    ```
    ```
    // OLD
    $container['registry'] = static function ($container) {
    return new Registry($container);
    };

    $container->registry->get(...)

    // NEW
    $flextype->container()['registry'] = static function () use ($flextype) {
    return new Registry($flextype);
    };

    $flextype->container('registry')->get(....)
    ```

* **core** class `Container` removed! ([#458](https://github.com/flextype/flextype/issues/458))

    We should use `$flextype` object to access all Flextype features inside Service Controllers and Models.

    Here is some examples:

    ```
    // OLD
    class FooController extends Container
    {
      public function bar()
      {
          return $this->registry->get('.....');
      }
    }

    // NEW
    class FooController
    {
      protected $flextype;

      public function __construct($flextype)
      {
          $this->flextype = $flextype;
      }

      public function bar()
      {
          return $this->flextype->container('registry')->get('.....');
      }
    }
    ```

<a name="0.9.9"></a>
# [0.9.9](https://github.com/flextype/flextype/compare/v0.9.8...v0.9.9) (2020-08-05)

### Features
* **core** Moving to PHP 7.2.5 #444
* **core** Added PhpArrayFileAdapter and set PhpArrayFile Cache as a default fallback cache driver instead of Filesystem Cache driver. This new feature give us performance boost up to 25%
* **core** Added preflight to Flextype basic checks and performance boost.
* **core** Updated all namespaces and core infrastructure. #437
* **core** Added Symfony Finder Component and `find_filter()` helper.
* **cache** Cache API improvements

    * Cache ID generation enhancements
    * Added new public function `fetchMultiple(array $keys)`
    * Added new public function `saveMultiple(array $keysAndValues, $lifetime = 0)`
    * Added new public function `deleteMultiple(array $keys)`
    * Added new public function `deleteAll()`
    * Added new public function `flushAll()`
    * Added new public function `purge(string $directory)`
    * Added new public function `purgeAll()`
    * Added new public function `getStats()`
    * Added new events `onCacheBeforePurgeAll`, `onCacheAfterPurgeAll`, `onCacheBeforePurge`, `onCacheAfterPurge`

* **core** New Media API for work with media uploads.

    New objects:

    ```
    $flextype['media_files_meta']
    $flextype['media_folders_meta']
    $flextype['media_files']
    $flextype['media_folders']
    ```

    See: http://docs.flextype.org/en/core/media

* **core** New simplified parsers and serializers functionality #438

    New objects:

    ```
    $flextype['markdown']
    $flextype['shortcode']
    $flextype['json']
    $flextype['yaml']
    $flextype['frontmatter']
    ```

    New methods:

    ```
    $flextype->markdown->parse(string $input)

    $flextype->shortcode->Added(string $name, $handler)
    $flextype->shortcode->parse(string $input, bool $cache = true)

    $flextype->json->decode(string $input, bool $cache = true, bool $assoc = true, int $depth = 512, int $flags = 0)
    $flextype->json->encode($input, int $options = 0, int $depth = 512) : string

    $flextype->yaml->decode(string $input, bool $cache = true, int $flags = 0) : array
    $flextype->yaml->encode($input, int $inline = 2, int $indent = 4, int $flags = 0) : string

    $flextype->frontmatter->decode(string $input, bool $cache = true)
    $flextype->frontmatter->encode($input) : string
    ```

* **entries** New simplified logic for entries methods: `fetch()` `fetchSingle()` and `fetchCollection()`

    We are stop doing unneeded things like extra scanning folders and files updates and etc... for fetching entries collections that's slowdowns the fetching process.

    We have improved filtering abilities for entries collections.

    From now there is a possible to path a much more rules for collections filtering inside the `fetchCollection()` and with using a standalone helper functions like `collect_filter()` and `find_filter()`

    * `public function fetch(string $path, bool $collection = false, $filter = []) : array`
       Fetch single entry or collections of entries.
    * `public function fetchSingle(string $path) : array`
       Fetch single entry.
    * `public function fetchCollection(string $path, $filter = []) : array`
       Fetch entries collection.

    See: http://docs.flextype.org/en/core/entries#methods

* **entries** New events Addeded for Entries API.

    ```
    onEntryCreate
    onEntryCopy
    onEntryRename
    onEntryDelete
    onEntryUpdate
    onEntryAfterCacheInitialized
    onEntryAfterInitialized
    onEntriesAfterInitialized
    ```

* **entries** New decoupled and configurable fields Addeded for entries instead of hardcoded.

    Entry fields decoupled into: `/flextype/Foundation/Entries/Fields/`

    Entry fields Addeded into `flextype.settings.entries.fields`

    ```
    fields:
      slug:
        enabled: true
      published_at:
        enabled: true
      published_by:
        enabled: true
      modified_at:
        enabled: true
      created_at:
        enabled: true
      created_by:
        enabled: true
      routable:
        enabled: true
      parsers:
        enabled: true
      visibility:
        enabled: true
      uuid:
        enabled: true
    ```

* **entries** Added ability to set individual cache control for specific entries.

    ```
    cache:
      enabled: true

    or

    cache:
      enabled: false
    ```

* **entries** Added new Entries API class properties.

    ```
    /**
     * Current entry path
     *
     * @var string
     * @access public
     */
    public $entry_path = null;

    /**
     * Current entry create data array
     *
     * @var array
     * @access public
     */
    public $entry_create_data = [];

    /**
     * Current entry create data array
     *
     * @var array
     * @access public
     */
    public $entry_update_data = [];
    ```

* **collections** New Collection functionality on top of Doctrine Collections.

    We are able to use collections for any type of items, not just for entries.
    New Collection are simple and powerful!

    See: http://docs.flextype.org/en/core/collections

* **settings** Set max_file_size 8mb for uploads.

* **yaml** YAML set default inline = 5 and indent = 2

* **vendors** New Arrays library for Accessing PHP Arrays via DOT notation.

* **rest-api** New Files Rest API.

    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/files | Fetch file(files) |
    | POST | /api/files | Upload file |
    | PUT | /api/files | Rename file |
    | PUT | /api/files/copy | Copy file |
    | DELETE | /api/files | Delete file |
    | PATCH | /api/files/meta | Updates file meta information |
    | POST | /api/files/meta | Updates file meta information |
    | DELETE | /api/files/meta | Delete file meta information |

* **rest-api** New Folders Rest API.

    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/folders | Fetch folder(folders) |
    | POST | /api/folders | Create folder |
    | PUT | /api/folders | Rename folder |
    | PUT | /api/folders/copy | Copy folder |
    | DELETE | /api/folders | Delete folder |

### Bug Fixes

* **frontmatter** Removed UTF-8 BOM if it exists.
* **frontmatter** Fixed line endings to Unix style.
* **entries** Fixed method `rename()` in Entries API #433
* **entries** Fixed issue with parsing content on entry fetch #441
* **rest-api** Fixed Rest API JSON Response #445
* **core** Fixed all namespaces #437
* **core** Fixed flextype config loading.
* **serializers** Fixed YAML native parser.
* **plugins** Fixed method `getPluginsCacheID()` for Plugins API

### Refactoring
* **pimple** Removed unused $flextype variable and cleanup dependencies.
* **yaml** save and mute error_reporting for native YAML parser.
* **cors** Removed unused parameter $args
* **plugins**  Removed dead variables.
* **shortcode** Updated return type for shortcode Added() method.
* **cache** Updated $driver type for DoctrineCache.

### Vendor Updates

* **core** Updated vendor league/glide to 1.6.0
* **core** Updated vendor doctrine/cache to 1.10.2
* **core** Updated vendor doctrine/collections to 1.6.6
* **core** Updated vendor respect/validation to 2.0.16
* **core** Updated vendor monolog/monolog to 2.1.1
* **core** Updated vendor thunderer/shortcode to 0.7.4
* **core** Updated vendor flextype-components/filesystem to 2.0.7
* **core** Updated vendor flextype-components/registry to 3.0.0
* **core** Updated vendor flextype-components/number to 1.1.1
* **core** Updated vendor composer/semver to 3.0.0
* **core** Updated vendor symfony/yaml to 5.1.3
* **core** Updated vendor ramsey/uuid to 4.1.0

### BREAKING CHANGES

* **entries** Wildcard * removed from parsers field.
* **entries** Cache setup removed from parsers field.
* **settings** `/project/config/settings.yaml` move to `/project/config/flextype/settings.yaml`
* **constants** Removed constant `PATH['config']`, use - `PATH_PROJECT . '/config/'`
* **core** Removed Date Component from the system.
* **core** Removed Text Component from the system.
* **cache** removed methods clear() and clearAll(), use purge() and purgeAll() instead.
* **cache** change return type for methods `save()`, `delete()` from void too bool.

<a name="0.9.8"></a>
# [0.9.8](https://github.com/flextype/flextype/compare/v0.9.7...v0.9.8) (2020-05-14)

### Features
* **core** New lightweight and powerful core for kickass Applications!
* **core** New Content Management API (CMA) for Entries. #421

    The Content Management API (CMA), is a read-write API for managing entries.

    You could use the CMA for several use cases, such as:

    * Automatic imports from WordPress, Joomla, Drupal, and more.
    * Integration with other backend systems, such as an e-commerce shop.
    * Building custom editing experiences.

    Endpoints for Content Management API:
    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/management/entries | Fetch entry(entries) |
    | POST | /api/management/entries | Create entry |
    | PATCH | /api/management/entries | Updated entry |
    | PUT | /api/management/entries | Rename entry |
    | PUT | /api/management/entries/copy | Copy entry(entries) |
    | DELETE | /api/management/entries | Delete entry |

    API Tokens folder: /project/tokens/management/entries

* **core** New Images API.

    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/images | Fetch image |

    API Tokens folder: /project/tokens/images

* **core** New Access API to create secret tokens for Content Management API (CMA).

    API Tokens folder: /project/tokens/access

* **core** Added Container for extending Flextype Container instead of Controller(s)
* **core** Added Application URL `url` into the common Flextype settings #405
* **core** Added new improved plugins sorting in the Plugins API.
* **core** Added dependencies validation for Plugins API #411
* **core** Added configurable CORS (Cross-origin resource sharing).

    ```
    cors:
      enabled: true
      origin: "*"
      headers: ["X-Requested-With", "Content-Type", "Accept", "Origin", "Authorization"]
      methods: [GET, POST, PUT, DELETE, PATCH, OPTIONS]
      expose: []
      credentials: false
    ```

* **core** Added manifest file `/src/flextype/config/flextype.yaml` for Flextype.
* **core** Added Serializer for data encoding/decoding and Parser for data parsing #424

### Bug Fixes

* **core** Fixed incorrect data merging of manifest and settings for plugins and themes #404

### BREAKING CHANGES

* **core** core decoupled in the plugins, and moved out of the Flextype release package!

    Install all needed plugins for your project by your self.
    Browse plugins: https://github.com/flextype-plugins

* **core** new way for data merging of manifest and settings for plugins and themes #404

    for e.g. this is a wrong code to access site title:
    ```
    {{ registry.plugins.site.title|e('html') }}
    ```

    and this is a correct code to access site title:
    ```
    {{ registry.get('plugins.site.settings.title')|e('html') }}
    ```
* **core** We should Added app `url` into the core instead of `base_url` and `site_url` #405

    for e.g. this is a wrong code to access site url:
    ```
    {{ registry.plugins.site.url }}
    ```

    and this is a correct code to access app url:
    ```
    {{ registry.get('flextype.settings.url') }}
    ```

* **core** new `project` folder instead of `site`

    - rename folder `site` into `project`
    - use new constant PATH_PROJECT instead of constant PATH['site']

* **core** removed constants

    - PATH['plugins']
    - PATH['themes']
    - PATH['entries']
    - PATH['themes']
    - PATH['snippets']
    - PATH['fieldsets']
    - PATH['tokens']
    - PATH['accounts']
    - PATH['uploads']

* **core** removed Snippets functionality

### Updated from Flextype 0.9.7 to Flextype 0.9.8

1. Backup your Site First!
2. Read BREAKING CHANGES section!
3. Download flextype-0.9.8.zip
4. Unzip the contents to a new folder on your local computer.
5. Removed on your server this folders and files:
    ```
    /flextype/
    /vendor/
    /index.php
    ```
6. Upload on your server this folders and files:
    ```
    /src/
    /vendor/
    /index.php
    ```
7. Rename `/site/` to `/project/`
8. Clear browser cache!
9. Create CDA, CMA and Access tokens for your project using this [webpage](https://awilum.github.io/flextype/en/api-token-generator).

<a name="0.9.7"></a>
# [0.9.7](https://github.com/flextype/flextype/compare/v0.9.6...v0.9.7) (2020-03-03)

### Features
* **core** Added Delivery API's for Entries, Images and Registry. #159

    Use Flextype as a Headless CMS with the full power of the Admin Panel.
    Build a Websites and Apps with a technology you are familiar with.

    Endpoints for Delivery API's:
    ```
    /api/delivery/entries
    /api/delivery/images
    /api/delivery/registry
    ```

* **core** Added new core constants `PATH['tokens']`, `PATH['accounts']`, `PATH['logs']`, `PATH['uploads']`
* **core** Added new locales support Persian, Indonesian, Galician #327
* **core** Added alternative comparison syntax for Entries API  

    Alternative comparison syntax:
    ```
    != - Not equals to
    like - Contains the substring
    ```

* **core** set entries field `routable`=`true` on new entry creation #320
* **core** use `array_merge()` instead of `array_replace_recursive()` for entries Updated method.
* **core** initialize plugins before themes #323
* **core** Updated Cache to use adapter to retrieve driver object #341
* **core** load Shortcodes extensions based on `flextype.shortcodes.extensions` array #352
* **core** load Twig extensions based on flextype.twig.extensions array #351
* **core** Added new Global Vars `PATH_ACCOUNTS`, `PATH_UPLOADS`, `PATH_TOKENS`, `PATH_LOGS` for Twig.
* **default-theme:** Moving to Tailwind CSS from Twitter Bootstrap #356
* **site-plugin:** Added ability to set custom site url, new shortcode `[site_url]` and twig var `{{ site_url }}`
* **form-plugin:** Added new Form plugin for forms handling instead of core Forms API.
* **icon-plugin:** Added new Icon plugin for Font Awesome icons set.

    usage in templates:
    ```
    <i class="icon">{{ icon('fab fa-apple') }}</i>
    ```

    usage in entries content:
    ```
    [icon value="fab fa-apple"]
    ```

* **(site-plugin):** Added ability to access `uri` variable in the theme templates.

    usage in templates:
    ```
    {{ uri }}
    ```

* **admin-plugin:** Added RTL support for URLs #62

    /site/config/plugins/admin/settings.yaml
    ```
    ...
    entries:
      slugify: true # set `false` to disable slugify for entries
    ```

* **admin-plugin:** Added ability to deactivate/activate all type of plugins. #211
* **admin-plugin:** Added Confirmation Required modal for system plugins deactivation.
* **admin-plugin:** new Admin Panel UI with better UX and powered by Tailwind CSS.
* **admin-plugin:** new improved entries media manager page.
* **admin-plugin:** Added ability to continue editing after saving in the editor.
* **admin-plugin:** Added action `onAdminThemeTail` for admin panel `base` layout.
* **admin-plugin:** Added ability to change entries view from `list view` to `table view`.

    /site/config/plugins/admin/settings.yaml
    ```
    ...
    entries:
      items_view_default: list # set `table` for table entries view
    ```

* **admin-plugin:** increase upload limit for `_uploadFile` from 3mb to 5mb
* **admin-plugin:** do not rewrite plugins and themes manifest with custom manifests.
* **admin-plugin:** Added parsleys for frontend form validation.
* **admin-plugin:** Added select2 for all select form controls.
* **admin-plugin:** Added swal for all modals.
* **admin-plugin:** Added flatpickr for date and time.
* **admin-plugin:** Added tippy.js for all tooltips and dropdown menus.
* **admin-plugin:** Added confirmation modals powered by swal for all critical actions.
* **admin-plugin:** Added dim color for entries with `draft`, `hidden` and `routable`=`false` status #324
* **admin-plugin:** Added ability to select entry type in the nice modal on entry creation. #331
* **admin-plugin:** Added new setting `entries.items_view_default` with default value `list`.
* **admin-plugin:** Added ability for redirect to the editor after creating #343
* **admin-plugin:** Added ability to create default API tokens on installation process.
* **admin-plugin:** Added ability to use local SVG version of Font Awesome Icons #322

    usage in templates:
    ```
    <i class="icon">{{ icon('fas fa-ellipsis-h') }}</i>
    ```

### Bug Fixes

* **core** Fixed discord server link #325
* **core** Fixed issue with system fields data types in the Entries API #383
* **admin-plugin:** Fixed issue for creating entry process with same IDs #333
* **admin-plugin:** Fixed redirect for entries after edit process.
* **admin-plugin:** Fixed issues with routable field on entry edit process.

### Refactoring

* **core** move `/site/cache directory` to the `/var/cache` #347
* **core** Removed Forms API from Flextype core #360
* **admin-plugin:** improve Gulp configuration for better assets building.
* **default-theme:** improve Gulp configuration for better assets building.
* **core** simplify logic for themes initialization process, Removed extra checks for theme setting is `enabled` or not.
* **admin-plugin:** move templates from `views` folder into the `templates` folder #347
* **admin-plugin:** Removed unused namespaces in EntriesContoller #347
* **admin-plugin:** Removed complex logic for themes activation process.
* **admin-plugin:** Added `ext-gd` to the require section of composer.json #347
* **admin-plugin:** Added `ext-fileinfo` to the require section of composer.json #347
* **admin-plugin:** Added `ext-dom` to the require section of composer.json #347
* **admin-plugin:** Added `ext-spl` to the require section of composer.json #347
* **default-theme:** Removed `enabled` option from theme settings.

### Vendor Updates
* **core** Updated vendor monolog/monolog to 2.0.2
* **core** Updated vendor cocur/slugify to 4.0.0
* **core** Updated vendor thunderer/shortcode to 0.7.3
* **core** Updated vendor ramsey/uuid to 3.9.2

### BREAKING CHANGES

* **core** accounts moved to their specific folders.

    for e.g.
    ```
    /accounts/admin.yaml => /accounts/admin/profile.yaml
    ```

* **core** Removed Debug, Html and Form Flextype Components.
* **core** all images links should be updated
    ```
    http://docs.flextype.org/en/content/media
    ```
* **core** core and plugin settings keys renamed
    ```
    For all core settings:
    settings.* => flextype.*

    For all site settings:
    settings.title => plugins.site.title
    settings.description => plugins.site.description
    settings.keywords => plugins.site.keywords
    settings.robots => plugins.site.robots
    settings.author.email => plugins.site.author.email
    settings.author.name => plugins.site.author.name
    ```

* **admin-plugin:** Removed Twitter Bootstrap from Admin Panel and Default Theme.
* **admin-plugin:** Removed user profile page `/admin/profile`
* **admin-plugin:** method `getUsers()` renamed to `getUsersList()` in UsersController.

<a name="0.9.6"></a>
# [0.9.6](https://github.com/flextype/flextype/compare/v0.9.5...v0.9.6) (2019-12-01)

### Features

* **core** Added ability to hide title for hidden fields #240
* **core** Added new public method delete() for Cache #308
* **core** Added CacheTwigExtension #309  

    usage in templates:
    ```
    {{ cache.CACHE_PUBLIC_METHOD }}
    ```

* **core** Added ability to override plugins default manifest and settings #224
* **core** Added ability to override themes default manifest and settings #256
* **core** Added ability to set help text for generated form controls #283  

    usage in fieldsets:
    ```
    help: "Help text here"
    ```

* **core** Added ability to store entry system fields in entries create method #247
* **core** Added alternative comparison syntax for Entries API  

    Alternative comparison syntax:
    ```
    eq - Equals
    neq - Not equals
    lt - Lower than
    lte - Lower than or equal to
    gt - Greater than
    gte - Greater than or equal to
    ```  
    docs: http://docs.flextype.org/en/themes/entries-fetch

* **core** Added `json_encode` and `json_decode` twig filter #289  

    usage in templates:
    ```
    // Result: {"title": "Hello World!"}
    {{ {'title': 'Hello World!'}|json_encode }}

    // Result: Hello World!
    {{ '{"title": "Hello World!"}'|json_decode.title }}
    ```

* **core** Added parser twig extension #262
* **core** Added new field property `default` instead of `value` #303
* **core** Added `yaml_encode` and `yaml_decode` twig filter #290  

    usage in templates:
    ```
    // Result: title: 'Hello World!'
    {{ {'title': 'Hello World!'}|yaml_encode }}

    // Result: Hello World!
    {{ 'title: Hello World!'|yaml_decode.title }}
    ```

* **core** Markdown parsing should be cached in production #287
* **core** YAML parsing will be cached in production #263
* **core** Refactor entries fetch methods naming #315  

    we have:  
    `fetch` - for single and collection entries request  
    `fetchSingle` - for single entry request.   
    `fetchCollection` - for collection entries request.  

* **core** Added routable option for entries #284  

    usage in entry:
    ```
    routable: false
    ```
    by default `routable` is `true`

* **admin-plugin:** Added help text for common form controls #280
* **admin-plugin:** Added icons for settings tabs sections #293
* **admin-plugin:** hide textarea control for codemirror editor #279
* **admin-plugin:** show themes title instead of themes id's on settings page #187
* **admin-plugin:** Added ability to set individual icons #250
* **admin-plugin:** Added ability to set individual icons for plugins #255
* **admin-plugin:** Added ability to work with entry custom fieldset #246
* **admin-plugin:** Added individual icons for snippets #253
* **admin-plugin:** Added individual icons for templates and partials #254
* **admin-plugin:** Added plugins settings page #258
* **admin-plugin:** Added themes settings page #296
* **admin-plugin:** show message on plugins page if no plugins installed #294
* **admin-plugin:** use dots icon for actions dropdown #292
* **admin-plugin:** Added auto generated slugs from title field #305
* **admin-plugin:** Added help tooltips #306
* **admin-plugin:** store Entires/Collections counter in cache #203
* **admin-plugin:** YAML parsing will be cached in production #263
* **admin-plugin:** Added ability to hide fieldsets from entries type select #304  

    usage in fieldsets:
    ```
    hide: true
    ```
    by default `hide` is `false`

* **site-plugin:** Added routable option for entries #284  


### Performance Improvements

* **core** Added realpath_cache_size to .htaccess
* **core** improve Plugins API - locales loading and increase app performance #259
* **core** improve Cache on production and increase app performance #290 #263  


### Bug Fixes

* **admin-plugin:** Fixed issue with saving entry source #251
* **admin-plugin:** Fixed file browser styles
* **admin-plugin:** Fixed breadcrumbs for theme templates
* **core** Entries API - Fixed Collection Undefined Index(s) for fetchAll method #243
* **core** Fixed broken logic for form inputs without labels #274
* **core** Fixed default and site settings loading #297
* **core** Fixed id's names for all generated fields #277
* **core** Fixed notice undefined index: created_at in Entries API
* **core** Fixed notice undefined index: published_at in Entries API #265
* **core** Fixed Plugins API - createPluginsDictionary method and increase app perfomance #259
* **core** Fixed state of active tabs for all generated forms #276
* **core** Fixed state of aria-selected for all generated forms #275  


### Vendor Updates
* **core** Updated vendor flextype-components/date to 1.0.0
* **core** Updated vendor symfony/yaml to 4.4.0
* **core** Updated vendor doctrine/cache to 1.10.0
* **core** Updated vendor doctrine/collections to 1.6.4
* **core** Updated vendor monolog/monolog to 3.12.3
* **core** Updated vendor bootstrap to 4.4.1
* **admin-plugin:** Updated vendor bootstrap to 4.4.1
* **admin-plugin:** Updated vendor trumbowyg to 2.20.0  


### BREAKING CHANGES

* **core** method fetchAll removed! please use `fetch`, `fetchSingle` or `fetchCollection`
* **core** changed and_where & or_where execution in the templates  

    FROM

    ```
    'and_where': {

    }

    'or_where': {

    }
    ```

    TO

    ```
    'and_where': [
        {

        }
    ]

    'or_where': [
        {

        }
    ]
    ```

* **core** Rename property `value` to `default` for all fieldsets where it is used.

<a name="0.9.5"></a>
# [0.9.5](https://github.com/flextype/flextype/compare/v0.9.4...v0.9.5) (2019-09-21)
### Bug Fixes

* **core** issue with cache in the Entries API - fetchAll method #234 2779777
* **core** issue with emitter twig function #234 426a073
* **core** issue with empty entries folder Entries API - fetchAll method #234 cf61f2d
* **core** issue with Cache ID for Themes list #234 594f4a3
* **admin-plugin:** issue with active button styles on Themes Manager page #234 434f336
* **admin-plugin:** issue with emitter twig function #234 806b18e
* **admin-plugin:** Russian translations #233
* **site-plugin:** notice for undefined $query['format'] #234 8bde8eb

### Code Refactoring
* **core** Removed $response from Forms render method #234
* **core** Added property forms to Flextype\EntriesController #234

### BREAKING CHANGES
Changed emitter execution in the templates

FROM
```
{{ emitter.emit('EVENT_NAME') }}
```

TO
```
{% do emitter.emit('EVENT_NAME') %}
```

<a name="0.9.4"></a>
# [0.9.4](https://github.com/flextype/flextype/compare/v0.9.3...v0.9.4) (2019-09-11)
### Added
* Flextype Core: Added ability to work with different types of content #212 #186
* Flextype Core: Added new filter `tr` for I18nTwigExtension #186
* Flextype Core: Added MARKDOWN, YAML and JSON parsers. #212 #186
* Flextype Core: Added YamlTwigExtension #186
* Flextype Core: Added ResponseTime Middleware #186
* Flextype Core: Added UUID (universally unique identifier) for all entries #197 #186
* Flextype Core: Added message for Glide if image not found #189 #186
* Flextype Core: Added victorjonsson/markdowndocs for generating markdown-formatted class documentation #186
* Flextype Core: Added custom callable resolver, which resolves PSR-15 middlewares. #213 #186
* Flextype Core: Added git commit message convention. #186
* Flextype Core: Added AuthMiddleware globally #201 #186
* Flextype Core: Added new twig options `debug` `charset` `cache` #186
* Flextype Core: Added new field `tags` #186
* Flextype Core: Added new field `datetimepicker` #186
* Flextype Core: Added block for all direct access to .md files in .htaccess #186
* Flextype Core: Added block access to specific file types for these user folders in .htaccess #186
* Flextype Core: Added new option date_display_format #186
* Flextype Admin Panel: Added Trumbowyg view html code #193 #186
* Flextype Admin Panel: Added tail section for base.html template #186
* Flextype Admin Panel: Added new event onAdminThemeFooter in base.html template #186
* Flextype Admin Panel: Added ability to set `published_at`, `created_at` for site entries #186
* Flextype Admin Panel: Added ability to set `created_by`, `published_by` for site entries #186
* Flextype Site Plugin: Added ability to get query params inside twig templates #186
* Flextype Site Plugin: Added ability to get entries in JSON Format #186
* Flextype Default Theme: Added ability to work with tags for default theme #186

### Fixed
* Flextype Core: Fixed ShortcodesTwigExtension issue with null variables #186
* Flextype Core: Fixed issue with bind_where expression for Entries fetchAll method #186
* Flextype Core: Fixed issue with and_where expression for Entries fetchAll method #186
* Flextype Core: Fixed issue with or_where expression for Entries fetchAll method #186
* Flextype Admin Panel: Fixed dark theme for admin panel #186 #168

### Changed
* Flextype Core: Moving to PHP 7.2 #198 #186
* Flextype Core: JsonParserTwigExtension renamed to JsonTwigExtension #186
* Flextype Core: Twig json_parser_decode renamed to json_decode #186
* Flextype Core: Twig json_parser_encode renamed to json_encode #186
* Flextype Core: Default theme - Updated assets building process and GULP to 4.X.X #206 #186
* Flextype Core: Default theme - theme.json converted to theme.yaml #201 #186
* Flextype Core: Default theme - settings.json converted to settings.yaml #201 #186
* Flextype Core: Site entries move from JSON to FRONTMATTER (MD) #212 #186
* Flextype Core: Entries - use getDirTimestamp for fetchAll method #212 #186
* Flextype Core: Entries - change private `_file_location()` to public `getFileLocation()` #186
* Flextype Core: Entries - change private `_dir_location()` to public `getDirLocation()` #186
* Flextype Core: Snippets - change private `_file_location()` to public `getFileLocation()` #186
* Flextype Core: Snippets - change private `_dir_location()` to public `getDirLocation()` #186
* Flextype Core: Fieldsets - change private `_file_location()` to public `getFileLocation()` #186
* Flextype Core: Fieldsets - change private `_dir_location()` to public `getDirLocation()` #186
* Flextype Core: Updated .gitignore
* Flextype Core: Updated copyrights information
* Flextype Core: Updated vendor flextype-components/filesystem to 2.0.6
* Flextype Core: Updated vendor flextype-components/date to 1.1.0
* Flextype Core: Updated vendor zeuxisoo/slim-whoops to 0.6.5
* Flextype Core: Updated vendor doctrine/collections to 1.6.2
* Flextype Core: Updated vendor slim/slim to 3.12.2
* Flextype Core: Updated vendor respect/validation to 1.1.31
* Flextype Core: Updated vendor monolog/monolog to 2.0.0
* Flextype Core: Updated vendor symfony/yaml to 4.3.4
* Flextype Site Plugin: settings.json converted to settings.yaml #201 #186
* Flextype Site Plugin: plugin.json converted to plugin.yaml #201 #186
* Flextype Site Plugin: en_US.json and ru_RU.json converted to en_US.yaml and ru_RU.yaml #201 #186
* Flextype Admin Panel: Settings page improvements #186
* Flextype Admin Panel: Installation page improvements #194 #186
* Flextype Admin Panel: Entries editor page improvements #186
* Flextype Admin Panel: settings.json converted to settings.yaml #201 #186
* Flextype Admin Panel: plugin.json converted to plugin.yaml #201 #186
* Flextype Admin Panel: en_US.json and ru_RU.json converted to en_US.yaml and ru_RU.yaml #201 #186
* Flextype Admin Panel: JS decoupled in partials from base.html #186
* Flextype Admin Panel: field `editor` changed to `html` #186
* Flextype Admin Panel: improve admin settings page #186

### Removed
* Flextype Core: Removed `date` field #196 #186
* Flextype Admin Panel: Removed save button on the media page #225 #186
* Flextype Admin Panel: Removed unused css code #186
* Flextype Admin Panel: Removed unused js code #186

<a name="0.9.3"></a>
# [0.9.3](https://github.com/flextype/flextype/compare/v0.9.2...v0.9.3) (2019-07-07)
### Fixed
* Flextype Core: Entries - issue with binding arguments inside method fetchAll() - fixed. #182
* Flextype Core: Entries - issue with possible boolean false result from Filesystem::getTimestamp() inside method fetchAll() - fixed. #182
* Flextype Core: Entries - issue with possible boolean false result from Filesystem::getTimestamp() inside method fetch() - fixed. #182
* Flextype Admin Panel: critical issue with possibility to register two admins! - fixed. #183 #182
* Flextype Admin Panel: Left Navigation - active state for Templates area - fixed. #182
* Flextype Default Theme: issue with `TypeError: undefined is not an object` for lightbox - fixed. #182
* Flextype Default Theme: Fixed thumbnail image for Default Theme #182

<a name="0.9.2"></a>
# [0.9.2](https://github.com/flextype/flextype/compare/v0.9.1...v0.9.2) (2019-07-06)
### Added
* Flextype Default Theme: pagination for blog entries Addeded. #164 #165
* Flextype Default Theme: New templates for entry Gallery - Addeded. #165
* Flextype Core: New Shortcode [registry_get] - Addeded. #165
* Flextype Core: New entry Gallery - Addeded. #165
* Flextype Core: New fieldsets for entry Gallery - Addeded. #165
* Flextype Core: Doctrine Collections - Addeded. #175 #165
* Flextype Core: GlobalVarsTwigExtension - new variable - `PHP_VERSION` - Addeded. #165
* Flextype Core: FilesystemTwigExtension - new function `filesystem_get_files_list` Addeded. #165
* Flextype Core: Snippets - new snippet `google-analytics` Addeded. #165
* Flextype Core: Fieldsets Content - menu_item_target fixed. #165
* Flextype Admin Panel: Show nice message if there is no items for current area. #158 #165
* Flextype Admin Panel: Tools - Addeded. #170 #165
* Flextype Admin Panel: Tools - Cache area Addeded. #170 #165
* Flextype Admin Panel: Tools - Registry area Addeded. #170 #165
* Flextype Admin Panel: Themes manager Addeded. #171 #165
* Flextype Admin Panel: New Translates Addeded. #165

### Changed
* Flextype Core: All Twig Extensions - refactored and updated. #165
* Flextype Core: Entries - new params `$id` and `$args` for `fetchAll()` method. #165
* Flextype Core: Entries - fetching methods updated and ready to work with Collections. #175 #165
* Flextype Core: Snippets Shortcode - renamed `snippets_fetch` to `snippets_exec`. #165
* Flextype Admin Panel: Entires - improved styles for fieldsets tabs. #165
* Flextype Admin Panel: Entires - styles for Fieldsets tabs improved #165
* Flextype Admin Panel & Flextype Core: Settings improvements #153 #165
* Flextype Admin Panel: Entries - show entry slug if entry default field is empty. #165
* Flextype Admin Panel: Stay on current page after saving. #155 #165

### Fixed
* Flextype Core & Admin and Default theme: wrong `emmiter_emmit` renamed to correct `emitter_emit` #165
* Flextype Admin Panel: Entries - issues with hardcoded admin url - fixed. #165
* Flextype Admin Panel: Entries - `PATH_FIELDSETS` used instead of hardcoded path. #165
* Flextype Admin Panel: Fixed all tabs state for Fieldsets, Snippets, Templates areas. #165
* Flextype Admin Panel: Entries - move functionality issues #179 #165

### Removed
* Flextype Admin Panel: Left Navigation - documentation link - removed #165

<a name="0.9.1"></a>
# [0.9.1](https://github.com/flextype/flextype/compare/v0.9.0...v0.9.1) (2019-06-18)
### Added
* Flextype Admin Panel: new setting `route` Addeded to customize admin base route. #154
* Flextype Core: GlobalVarsTwigExtension - new global constant `PATH_FIELDSETS` Addeded. #154
* Flextype Core: Entries API - public property `$entry` Addeded. #154
* Flextype Core: Entries API - public property `$entries` Addeded. #154
* Flextype Core: Entries API - new event `onEntryAfterInitialized` Addeded. #154
* Flextype Core: Entries API - new event `onEntriesAfterInitialized` Addeded. #154
* Flextype Core: Shortcodes - `EntriesShortcode` Addeded. #154
* Flextype Core: Shortcodes - `BaseUrlShortcode` Addeded. #154
* Flextype Core: Snippets - SnippetsTwigExtension: `snippets_exec()` Addeded. #154
* Flextype Core: Snippets - `[snppets_fetch]` shortcode Addeded. #154
* Flextype Core: Snippets - `_exec_snippet()` method Addeded. #154
* Flextype Core: Snippets - `exec()` method Addeded. #154
* Flextype Core: Snippets - Addeded ability to access $flextype and $app inside snippets. #154
* Flextype Core: GlobalVarsTwigExtension `FLEXTYPE_VERSION` Addeded. #154
* Flextype Site Plugin: public property `$entry` Addeded. #154
* Flextype Site Plugin: new event `onSiteEntryAfterInitialized` Addeded. #154

### Fixed
* Flextype Core: Entries API - `fetchALL()` issue with fetching entries recursively fixed. #154 #161

### Changed
* Flextype Site: code refactoring. #154
* Flextype Admin Panel: code refactoring. #154
* Flextype Core: Snippets - from now we will set prefix `bind_` for all variables. #154

### Removed
* Flextype Core: Entries API - Removed unused Shortcodes code from method `fetch()` #162
* Flextype Core: Shortcodes - `SiteUrlShortcode` removed. #154
* Flextype Core: Snippets - `SnippetsTwigExtension`: snippet removed. #154
* Flextype Core: Snippets - `[snippets]` shortcode removed. #154
* Flextype Core: Snippets - `_display_snippet()` method removed. #154
* Flextype Core: Snippets - `- display()` method removed. #154
* Flextype Core: GlobalVarsTwigExtension `flextype_version` removed. #154

<a name="0.9.0"></a>
# [0.9.0](https://github.com/flextype/flextype/compare/v0.8.3...v0.9.0) (2019-06-14)
### Added
* Flextype Core: Slim Framework Integration!
* Flextype Core: Twig Template Engine Integration!
* Flextype Core: Whoops Error Handler Integration!
* Flextype Core: Monolog library Integration!
* Flextype Core: Slugify library Integration!
* Flextype Core: Complete Glide/Intervention Image Implemented for Image manipulation on fly!
* Flextype Core: New Event handler from The League of Extraordinary Packages for better event handling.
* Flextype Core: New Entries API
* Flextype Core: New Fieldsets API
* Flextype Core: New Snippets API
* Flextype Core: New Plugins API
* Flextype Core: New JSON Parser instead of old YAML Parser.
* Flextype Core: Using new languages files format and JSON extension instead of YAML.
* Flextype Core: Using JSON extension instead of YAML for all kind of data to store.
* Flextype Core: New CSRF service for better cross-site request forgery protection.
* Flextype Core: composer.json ext-json and ext-mbstring Addeded into require section.
* Flextype Core: composer.json suggest section Addeded.
* Flextype Core: composer.json: apcu-autoloader Addeded for APCu cache as a fallback for the class map.
* Flextype Site: New plugin Site Addeded.
* Flextype Core: Respect Validation - The most awesome validation engine ever created for PHP - Addeded.
* Flextype Admin Panel: New admin panel plugin based on Slim Framework.
* Flextype Admin Panel: Fieldset Sections(groups) Addeded.
* Flextype Admin Panel: New Field types - select, editor (instead of html)

### Changed
* Flextype Core: Thunderer Shortcodes don't parse fields by default, need to use filter.
* Flextype Core: Thunderer Shortcodes updated to 0.7.2.
* Flextype Core: Flextype Components Arr updated to 1.2.5
* Flextype Core: Flextype Components Number updated to 1.1.0
* Admin Panel: Bootstrap updated to 4.3.1
* Admin Panel: Codemirror updated to 5.43.0
* Admin Panel: Trumbowyg updated to 2.18.0
* Admin Panel: Settings Manager - locales list - improvements!

### Fixed
* Admin Panel: Translates fixes.
* Admin Panel: Issue with js error for codemirror - fixed.
* Flextype Core: Plugins API - issue with plugins list fixed.

### Removed
* Flextype Admin Panel: Menus Manager removed.
* Flextype Core: YAML Parser removed.
* Flextype Core: Symfony YAML Component removed.
* Flextype Core: Flextype Token Component removed.
* Flextype Core: Flextype Notification Component removed.
* Flextype Core: Flextype Error Handler Component removed.
* Flextype Core: Flextype Event Component removed.

<a name="0.8.3"></a>
# [0.8.3](https://github.com/flextype/flextype/compare/v0.8.2...v0.8.3) (2019-01-16)
### Added
* Admin Panel: New Gorgeous Light Theme for Admin panel!
* Admin Panel: Settings Manager - ability to change admin panel theme - Addeded.
* Admin Panel: Settings Manager - Select dropdown for cache driver - Addeded.
* Flextype Core: Cache - new cache driver Array - Addeded.
* Flextype Core: Cache - new cache driver SQLite3 - Addeded.
* Flextype Core: Cache - new cache driver Zend - Addeded.

### Changed
* Flextype Core: Symfony Yaml updated to 4.2.2.
* Admin Panel: Settings Manager - cache settings editing - improvements.
* Flextype Core: default settings - refactoring.

### Fixed
* Flextype Core: Cache - issue with selecting current driver_name - fixed.
* Admin Panel: Dark Theme fixes.
* Admin Panel: Translates fixes.
* Admin Panel: Typo fixes.

<a name="0.8.2"></a>
# [0.8.2](https://github.com/flextype/flextype/compare/v0.8.1...v0.8.2) (2019-01-09)
### Added
* Admin Panel: ClipboardJS Addeded!
* Admin Panel: Media Manager - Twitter Bootstrap File browser - Addeded.
* Admin Panel: Snippets Manager: Embeded code info modal Addeded.
* Admin Panel: Settings Manager - Select dropdown for default entry - Addeded.
* Admin Panel: Settings Manager - Select dropdown for timezones - Addeded.
* Admin Panel: Settings Manager - Select dropdown for themes - Addeded.

### Changed
* Flextype Core: Text Components updated to 1.1.2.
* Admin Panel: Entries Manager - upload file size increased to 27000000 bytes.

### Fixed
* Flextype Core: Default theme - issue with package.json - fixed.
* Flextype Core: Code fixes according to the phpDocumentor.
* Flextype Core: Default theme - settings and manifest - fixes.
* Admin Panel: Translates fixes.

<a name="0.8.1"></a>
# [0.8.1](https://github.com/flextype/flextype/compare/v0.8.0...v0.8.1) (2019-01-07)
### Added
* Flextype Core: Glide/Intervention Image Implemented for Image manipulation!
* Flextype Core: Cache - ability to delete glide cache folder Addeded.

### Changed
* Flextype Core: Thunderer Shortcode updated to 0.7.0 - over 10x performance and memory usage improvement!
* Flextype Core: Default settings updates.
* Flextype Core: Arr Components updated to 1.2.4.
* Flextype Core: Default theme - Twitter Bootstrap Updated to 4.2.1
* Admin Panel: Media Manager - uploader improvements
* Admin Panel: Menus Manager - menus name are clickable now.
* Admin Panel: Fieldsets Manager - fieldsets name are clickable now.
* Admin Panel: Templates Manager - templates and partials name are clickable now.
* Admin Panel: Snippets Manager - snippets name are clickable now.
* Admin Panel: Settings Manager - look and feel improvements.
* Admin Panel: Twitter Bootstrap Updated to 4.2.1

### Fixed
* Admin Panel: Snippets Manager - shortcode issue - fixed.
* Admin Panel: gulpfile - issue with duplicated codemirror - fixed.
* Admin Panel: Trumbowyg styles fixes.
* Admin Panel: Plugins Manager - issue with broken homepage url in the Info Modal - fixed.

<a name="0.8.0"></a>
# [0.8.0](https://github.com/flextype/flextype/compare/v0.7.4...v0.8.0) (2018-12-28)
### Added
* Flextype Core: To improve engine flexibility was decided to use entity name Entries/Entry instead of entity name Pages/Page.
* Flextype Core: New folder `/site/entries/` Addeded.
* Flextype Core: New entry variable `base_url` Addeded.
* Flextype Core: Snippets functionality Addeded.
* Flextype Core: New constant PATH['snippets'] Addeded for Snippets.
* Flextype Core: New folder `/site/snippets/` Addeded.
* Flextype Core: Menus functionality Addeded.
* Flextype Core: New folder `/site/menus/` Addeded.
* Flextype Core: Fieldsets functionality Addeded.
* Flextype Core: Fallback functionality for settings Addeded.
* Flextype Core: New settings item `accept_file_types` Addeded.
* Flextype Core: Common PHP Overrides Addeded to .htaccess
* Flextype Core: Custom YamlParser with native support to increase system performance Addeded.
* Flextype Core: Ability to get hidden entries for method getEntries() Addeded.
* Flextype Core: New setting options `entries.error404` for error404 page Addeded.
* Admin Panel: Fieldsets Manager Addeded.
* Admin Panel: Menus Manager Addeded.
* Admin Panel: Snippets Manager Addeded.
* Admin Panel: Templates Manager Addeded.
* Admin Panel: Entries Manager with nice one level tree view for pages list Addeded.
* Admin Panel: Português locale Addeded.
* Admin Panel: General - trumbowyg - table plugin Addeded.
* Flextype new Default Theme with predefined Fieldsets and Entries templates Addeded.

### Changed
* Flextype Core: Plugins - Locales Array updated.
* Flextype Core: Form Components updated to 1.0.2.
* Flextype Core: Filesystem Components updated to 1.1.5.
* Flextype Core: Content - used new updated getFilesList() method.
* Flextype Core: Updated order of params in getEntries() method.
* Admin Panel: Settings Manager - display only available locales.
* Admin Panel: Profile Manager - improvements for profile look and feel.
* Admin Panel: Entries Manager * Form Genetator will not create labels for hidden inputs.
* Admin Panel: Plugins Manager - Get More Plugins button moved to the right.
* Admin Panel: General - trumbowyg editor styles updates.
* Admin Panel: General - trumbowyg updated to 2.13.0
* Admin Panel: Font Awesome updated to 5.6.3.
* Admin Panel: General - Messenger moved to to the bottom.
* Admin Panel: General - updates according to the php template syntax guidelines.
* Admin Panel: Pages Manager - use defined file types (extensions to be exact) that are acceptable for upload.

### Fixed
* Flextype Core: Content - Issue with returned value of Arr::sort() fixed.
* Admin Panel: General - translates fixes.

### Removed
* Flextype Core: Blueprints functionality removed.
* Flextype Core: Pages functionality removed.
* Flextype Core: Error404 page removed from `/site/entries/` folder.
* Flextype Core: Folder `/site/pages/` removed.
* Flextype Core: Dot from `date_format` setting removed.
* Flextype Simple Theme removed.

<a name="0.7.4"></a>
# [0.7.4](https://github.com/flextype/flextype/compare/v0.7.3...v0.7.4) (2018-12-14)
* Content: new frontMatterParser() - Addeded
* Config: set error reporting - false
* Updated theme simple according to the php template syntax guidelines
* Super heavy "imagine/imagine": "1.2.0" - removed
* Flextype Component - Errorhandler updated to 1.0.5

<a name="0.7.3"></a>
# [0.7.3](https://github.com/flextype/flextype/compare/v0.7.2...v0.7.3) (2018-12-13)
* Content: visibility hidden for pages - Addeded
* Settings merged into one settings.yaml file
* Using Imagine library for image manipulation
* Flextype Component - I18n updated to 1.2.0
* Flextype Component * Filesystem updated to 1.1.3
* Symfony YAML - updated to 4.2.1

<a name="0.7.2"></a>
# [0.7.2](https://github.com/flextype/flextype/compare/v0.7.1...v0.7.2) (2018-11-24)
* Flextype Component - Cookie updated to 1.2.0
* Flextype Component * Filesystem updated to 1.1.2

<a name="0.7.1"></a>
# [0.7.1](https://github.com/flextype/flextype/compare/v0.7.0...v0.7.1) (2018-11-17)
* Plugins: New method getLocales() Addeded
* Content: processPage() - ability to ignore content parsing - Addeded.

<a name="0.7.0"></a>
# [0.7.0](https://github.com/flextype/flextype/compare/v0.6.1...v0.7.0) (2018-11-16)
* Updated Symfony YAML to 4.1.1
* Updated Text Component to 1.1.0
* Updated Session Component to 1.1.1
* Updated Doctrine Cache to 1.8.0
* Updated I18n Component to 1.1.0
* Updated Token Component to 1.2.0
* Content: field 'published' changed to 'visibility'
* Plugins: from now no need to Added plugin names manually to the site.yaml
* Plugins: Addeded ability to load plugins settings.yaml file
* Plugins: from now plugins configurations stored in the plugin-name/settings.yaml file
* Added system.yaml config file and use it for system configurations
* Themes: Addeded ability to load themes settings.yaml file
* Themes: from now themes configurations stored in the theme-name/settings.yaml file

<a name="0.6.1"></a>
# [0.6.1](https://github.com/flextype/flextype/compare/v0.6.0...v0.6.1) (2018-06-17)
* Fixed issue with not found pages status code
* Fixed Singleton classes and methods visibility changed from protected to private
* Added require vendors versions in composer.json
* Fixed Simple Theme styles

<a name="0.6.0"></a>
# [0.6.0](https://github.com/flextype/flextype/compare/v0.5.0...v0.6.0) (2018-06-09)
* Content: Markdown(Parsedown) parser removed! From now we are using plain HTML + Shortcodes
* Theme Simple: Cross-site scripting Vulnerabilities fixes
* Improving main .htaccess
* Code cleanup and refactoring #5

<a name="0.5.0"></a>
# [0.5.0](https://github.com/flextype/flextype/compare/v0.4.0...v0.5.0) (2018-06-03)
* Delete folders: site/data and site/accounts
* Delete folders: site/blocks and site/cache and site/logs
* Constants: accounts, blocks, data - removed.
* Flextype: new method setSiteConfig() Addeded
* Flextype: new method setErrorHandler() updates
* Flextype: new method setErrorHandler() Addeded
* Content: new protected method initParsers()
* Content: Blocks functionality removed - use Block Plugin
* Content: Section shortcode removed - use Section plugin
* Content: Site Url shortcode removed - use Site Url plugin
* Content: Registry shotcode remobed - use Registry plugin
* Content: Prevents automatic linking of URLs for Markdown parser
* Content: Method registerDefaultShortcodes() removed

<a name="0.4.4"></a>
# [0.4.4](https://github.com/flextype/flextype/compare/v0.4.3...v0.4.4) (2018-05-29)
* Content: Addeded ability to work with CONTENT SECTIONS with help of shortcodes [section] and [section_create]
* Content: getPage() method will only return data about requested page and will not insert them in global $page array.
* Content: events: onPageContentAfter and onPageContentRawAfter was removed from getPage(), use event onCurrentPageBeforeDisplayed instead.
* Site Config: new items Addeded: robots and description
* Theme Simple: Using Assets Component for css and js
* Theme Simple: New head meta Addeded: description, keywords, robots, generator
* Theme Simple: Meta charset getting from registry site.charset
* Theme Simple: Fixed issue with broken paths for JS
* Theme Simple: gulpfile: build process updated
* Theme Simple: package.json: Addeded gulp-concat and gulp-sourcemaps

<a name="0.4.3"></a>
# [0.4.3](https://github.com/flextype/flextype/compare/v0.4.2...v0.4.3) (2018-05-28)
* Content: set text/html request headers for displayCurrentPage() method
* Content: processCurrentPage() method Addeded
* Content: event names changed: onPageBeforeRender to onCurrentPageBeforeProcessed
* Content: event names changed: onPageAfterRender to onCurrentPageAfterProcessed
* robots.txt file was removed, use Robots plugin instead
* Code cleanup and refactoring #5

<a name="0.4.2"></a>
# [0.4.2](https://github.com/flextype/flextype/compare/v0.4.1...v0.4.2) (2018-05-22)
* Settings: cache.enabled is true from now
* Content: new methods Addeded: initShortcodes() initMarkdown() markdown()
* Events: new events Addeded: onMarkdownInitialized and onShortcodesInitialized

<a name="0.4.1"></a>
# [0.4.1](https://github.com/flextype/flextype/compare/v0.4.0..v0.4.1) (2018-05-20)
* Fixing issues with cache for getPages() method.
* Fixing issues with processPage() method.
* Fixing issues with all public methods in Cache class, from now all methods are static.
* Setting site.pages.flush_cache was removed from site.yaml file.

<a name="0.4.0"></a>
# [0.4.0](https://github.com/flextype/flextype/compare/v0.3.0...v0.4.0) (2018-05-16)
* Using SCSS for Simple theme
* Using Flextype Form Component
* Themes: class bug fixes
* Plugins: access for $locales changed to private
* Plugins: cache bug fixes
* New powerful Content class Addeded for working with content instead of Pages, Shortcode, Markdown
* Content: new page field: `published` Addeded
* Content: method for page blocks Addeded
* Content: cache Addeded for pages and blocks
* define CONSTANTS in PHP7 style
* Themes: changing from template() to view()

<a name="0.3.0"></a>
# [0.3.0](https://github.com/flextype/flextype/compare/v0.2.1...v0.3.0) (2018-05-05)
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
* ErrorHandler Addeded with errors logs.
* Code cleanup and refactoring #5

<a name="0.2.1"></a>
# [0.2.1](https://github.com/flextype/flextype/compare/v0.2.0...v0.2.1) (2018-03-26)
* date_format setting Addeded to /site/config.site.yml
* Pages: Fixed bug with pages sort and slice in getPages() method
* Pages: Fixed bug with pages list for /pages folder
* Pages: Fixes for generating page url field
* Pages: Added ability to create date field automatically for pages if date field is not exists.
* Code cleanup and refactoring #5

<a name="0.2.0"></a>
# [0.2.0](https://github.com/flextype/flextype/compare/v0.1.0...v0.2.0) (2018-03-23)
* Thunderer Shortcode Framework - Addeded
* Cache Flextype::VERSION for cache key - Addeded
* flextype/boot/shortcodes.php	- removed
* flextype/boot/events.php - removed
* Code cleanup and refactoring #5

<a name="0.1.0"></a>
# [0.1.0](https://github.com/flextype/flextype) (2018-03-21)
* Initial Release
