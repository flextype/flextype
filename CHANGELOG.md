# Release Notes for 1.x

<a name="1.0.0-alpha.2"></a>
# [1.0.0-alpha.2](https://github.com/flextype/flextype/compare/v1.0.0-alpha.1...v1.0.0-alpha.2) (2022-09-13)

### Features

* **rest-api** Added new Query API [587](https://github.com/flextype/flextype/issues/587).

* **entries** Added new private fields functionality for entries [585](https://github.com/flextype/flextype/issues/585).
  * Private field starts with `_` and evailable only in the current entry.

  **entry.md**
  ```yaml
  ---
  title: Product item
  _message: Message...
  _vars:
    currency: "USD"
    vat: "@type[int] [[ strings().random(2, 1234567890) ]]"
  price: "[[ 100 + _vars.vat ]]"
  price_with_currency: "[[ price ~ ' ' ~ _vars.currency ]]"
  ---

  [[ title ]] // Product item

  [[ _message ]] // Message...
  ```

  **response**
  ```yaml
  {
    "title": "Product item",
    "price": "120",
    "price_with_currency": "120 USD"
    "content": "Product item \n Message..."
  }
  ```

* **vars** Added new `Vars` service to store global variables.

* **directives** Added ability to disable expressions using `!expressions`.

* **directives** Added ability to disable shortcodes using `!shortcodes`.

* **directives** Added ability to disable markdown using `!markdown`.

* **directives** Added ability to disable textile using `!textile`.

* **directives** Added ability to disable php using `!php`.

* **directives** Added ability to disable types using `!types`.

* **expressions** Expressions language as a part of Parsers [586](https://github.com/flextype/flextype/issues/586).
  * From now Expressions are part of Parsers, configurable and available globally.
  * Ability to configure opening/closing tags for variables, blocks and comments.
  ```yaml
    opening_variable_tag: "[["
    closing_variable_tag: "]]"
    opening_block_tag: "[%"
    closing_block_tag: "%]"
    opening_comment_tag: "[#"
    closing_comment_tag: "#]"
  ```
  * Ability to write multiline expressions
  ```yaml
    [[ 
      field1 ~
      field2 ~
      field2
    ]]
  ```
  * Ability to store parsed expressions in the cache.
  * Ability to quickly access current entries fields.
  ```yaml
    [[ field_name ]]
  ``` 

* **expressions** Added support for `Vars` service. [583](https://github.com/flextype/flextype/issues/583)
  - New function `vars()` returns instance of `Vars` service.
  - New function `var()` for quick access to variables stored in `Vars` service.

* **shortcodes** Added support for `Vars` service. [583](https://github.com/flextype/flextype/issues/583)
  - New shortcode `(var)` to get, set, unset and delete variables from `Vars` service.

* **core** Added new package `Guzzle`.

* **core** Added a new `fetch` helper, expression function and shortcode with the ability to fetch data from different sources, entries, files, and URLs. [581](https://github.com/flextype/flextype/issues/581)

### Bug Fixes

* **expressions** Fixed `strings` expression function.

* **expressions** Fixed `Entries` Expressions methods.

* **helpers** Fixed issue in `collection` helper with limit and offset double check.

### BREAKING CHANGES
 
 * **expressions** Configuration for expressions moved from entries to parsers section.

 * **expressions** Use `parsers()->expressions()` instead of `expressions()` to access methods.

 * **entries** Local `vars` are replaced with global variables and local private fields.

 * **shortcodes** Use `registerShortcodes()` instead of `initShortcodes()` to register custom shortcodes.

* **shortcodes** Use `registerDirectives()` instead of `initDirectives()` to register custom directives.

* **shortcodes** Use `initExpressions()` instead of `registerExpressions()` to register custom expressions.

<a name="1.0.0-alpha.1"></a>
# [1.0.0-alpha.1](https://github.com/flextype/flextype/compare/v0.9.16...v1.0.0-alpha.1) (2022-07-12)

### Features

* **core** Minimum PHP version required PHP 8.1.0.

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
      about                       Get information about Flextype.
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

* **core** Added ability to execute specific project related code.

  - `before-plugins` to bootstrap file before plugins intialization.
  - `after-plugins` to bootstrap file after plugins intialization.

* **core** Added new core constants: `FLEXTYPE_PROJECT_NAME`, `FLEXTYPE_PATH_PROJECT`, `FLEXTYPE_PATH_TMP`, `FLEXTYPE_START_TIME`. 

* **core** Added ability to run Flextype in silent mode by disabling `app` and `cli`. 

* **core** Added New [Glowy PHP](https://awilum.github.io/glowyphp/) Packages `View`, `Macroable`, `Strings`, `Arrays`, `Csrf`, `Filesystem`, `Registry`, `Session`.

* **core** Added built-in I18n module.

* **core** Added ability to override default constants

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

    `src/flextype/settings.yaml`
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

    `src/flextype/settings.yaml`
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

    `src/flextype/settings.yaml`
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
            path: "src/flextype/core/Parsers/Shortcodes/UrlShortcode.php"
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
            path: "project/plugins/your-custom-plugin/Parsers/Shortcodes/UrlShortcode.php"
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

* **shortcodes** Added new shortcode `(getProjectUrl)` to get project url.

* **shortcodes** Added new shortcode `(getBaseUrl)` to get base url.

* **shortcodes** Added new shortcode `(getBasePath)` to get base path.

* **shortcodes** Added new shortcode `(getAbsoluteUrl)` to get absolute url.

* **shortcodes** Added new shortcode `(url)` to get url.

* **shortcodes** Added new shortcode `(urlFor)` to get url for route.

* **shortcodes** Added new shortcode `(getUriString)` to get uri string.

* **shortcodes** Added new shortcode `(filesystem)` to do filesytem manipulations.

* **shortcodes** Added new shortcode `(date)` to get date.

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

* **expressions** Added new expression function `max` to return the highest value in an array, or the highest value of several specified values.

* **expressions** Added new expression function `min` to return the lowest value in an array, or the lowest value of several specified values.

* **expressions** Added new expression function `ceil` to round a number up to the nearest integer.

* **expressions** Added new expression function `floor` to round a number down to the nearest integer.

* **expressions** Added new expression function `parsers` to get parsers service.

* **expressions** Added new expression function `serializers` to get serializers service.

* **expressions** Added new expression function `registry` to get registry service.

* **expressions** Added new expression function `slugify` to get slugify service.

* **expressions** Added new expression function `date` to get date.

* **expressions** Added new expression function `url` to get the url.

* **expressions** Added new expression function `urlFor` to get the url for a named route.

* **expressions** Added new expression function `fullUrlFor` to get the full url for a named route.

* **expressions** Added new expression function `isCurrentUrl` to determine is current url equal to route name.

* **expressions** Added new expression function `getCurrentUrl` to get current path on given Uri.

* **expressions** Added new expression function `getBasePath` to get the base path.

* **expressions** Added new expression function `getBaseUrl` to get the base url.

* **expressions** Added new expression function `getAbsoluteUrl` to get the absolute url.

* **expressions** Added new expression function `getProjectUrl` to get the project url.

* **expressions** Added new expression function `getUriString` to get the uri string.

* **expressions** Added new expression function `redirect` to create redirect.

* **cache** Added new cache driver `Phparray` to store cache data in raw php arrays files.

* **cache** Added router cache.

* **cache** Added ability to set custom cache ID string for `entries`, `parsers` and `serializers`.

* **tokens** Added new Tokens API.

* **helpers** All core helpers are located in the `src/flextype/helpers/`.

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

* **helpers** Added helper function `url` to get url.

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

* **macros** All core macros are located in the `src/flextype/macros/`.

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

* **core** Use new constant `FLEXTYPE_PATH_PROJECT` instead of `PATH['project']` and `FLEXTYPE_PATH_TMP` instead of `PATH['tmp']`.

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

* **tokens** Project tokens moved from `project/tokens` to `project/entries/tokens`.

* **entries** Changes for etnries memory storage.

  * use `entries()->registry()->get()` instead of `flextype('entries')->storage()->get()`
  * use `entries()->registry()->set()` instead of `flextype('entries')->storage()->set()`
  * use `entries()->registry()->has()` instead of `flextype('entries')->storage()->has()`
  * use `entries()->registry()->delete()` instead of `flextype('entries')->storage()->delete()`

  note: all method from Glowy PHP Arrays are available for Arrays Storage Object manipulations
  docs: https://github.com/glowyphp/arrays

### Refactoring

* **core** General code refactoring and improvements.

* **tests** All unit tests were rewritten.
