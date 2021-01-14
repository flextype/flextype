<a name="0.9.16"></a>
# [0.9.16](https://github.com/flextype/flextype/compare/v0.9.15...v0.9.16) (2020-01-14)

### Features

* **parsers** add commonmark instead of parsedown ([#540](https://github.com/flextype/flextype/issues/540))
* **shortcodes** add new shortcode - media_files_fetch

    example:

    ```
    [media_files_fetch id="entries/home/foo.txt" field="title" default="Bar"]
    ```

### Bug Fixes

* **bootstrap** fix include path for dependencies.

### Refactoring

* **core** general code refactoring and improvements.

<a name="0.9.15"></a>
# [0.9.15](https://github.com/flextype/flextype/compare/v0.9.14...v0.9.15) (2020-01-03)

### Features

* **media** add method `has()` for Media Folders ([#534](https://github.com/flextype/flextype/issues/534))
* **entries** simplify functionality to work with online entries storage. ([#536](https://github.com/flextype/flextype/issues/536))
* **parsers** move markdown and shortcode settings under parsers setting. ([#539](https://github.com/flextype/flextype/issues/539))

### Bug Fixes

* **entries** fix issue with individual entries cache field ([#537](https://github.com/flextype/flextype/issues/537))
* **plugins** fix issue with empty manifest and settings yaml files ([#538](https://github.com/flextype/flextype/issues/538))

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

* **core** fix issue with Rest API endpoints detection. ([#522](https://github.com/flextype/flextype/issues/522))

* **entries** fix issue with empty variable $data in fetch() method. ([#531](https://github.com/flextype/flextype/issues/531))

* **entries** fix issue with deleteStorage() method return data.

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

    Media Files API is macroable and we will able to add any custom fetch methods for receiving data from different sources.

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

    Media Folders API is macroable and we will able to add any custom fetch methods for receiving data from different sources.

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

    Entries API is macroable and we will able to add any custom fetch methods for receiving data from different sources.

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

* **fields** add new field `registry.get` for Registry API ([#494](https://github.com/flextype/flextype/issues/494))

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

* **fields** add new field `entries.fetch` for Entries API ([#492](https://github.com/flextype/flextype/issues/492))

    Entries API provides methods for entries fetch: `fetch()` and we should able to access them inside entries frontmatter header for fetching data right in the entries. Also, we will able to add and use any kind of fetch methods with our macroable functionality.  

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

* **fields** add new field `media.files.fetch` and `media.folders.fetch` for Media API's ([#501](https://github.com/flextype/flextype/issues/501)) ([#500](https://github.com/flextype/flextype/issues/500))

    Media API's provides methods for files and folders fetch: `fetch()` and we should able to access them inside entries frontmatter header for fetching data right in the entries. Also, we will able to add and use any kind of fetch methods with our macroable functionality.

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


* **entries** add new method `deleteStorage()` for Entries API ([#498](https://github.com/flextype/flextype/issues/498))

* **entries** add new method `hasStorage()` for Entries API ([#497](https://github.com/flextype/flextype/issues/497))

* **core** add new method `isApiRequest` to Determine API Request in the basic core functionality. ([#507](https://github.com/flextype/flextype/issues/507))

* **rest-api-entries** add ability to send options for `fetch()` methods in Entries Rest API. ([#504](https://github.com/flextype/flextype/issues/504))

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

* **rest-api-entries** add ability to call macroable fetch methods. ([#505](https://github.com/flextype/flextype/issues/505))

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

* **rest-api-media** add ability to call macroable fetch methods for Folder. ([#512](https://github.com/flextype/flextype/issues/512))

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

* **rest-api-media** add ability to call macroable fetch methods for Files. ([#513](https://github.com/flextype/flextype/issues/513))

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

* **fields** fix issue with slug field in Entries API ([#520](https://github.com/flextype/flextype/issues/520))

* **core** fix issue with invalid timezone setting ([#490](https://github.com/flextype/flextype/issues/490))

* **entries** fix issue with not exists entries collections. ([#503](https://github.com/flextype/flextype/issues/503))

* **entries** fix issue with collisions in Entries API $storage for entries fetching. ([#496](https://github.com/flextype/flextype/issues/496))

* **rest-api-entries** fix issue with 404 status code in Entries Rest API ([#502](https://github.com/flextype/flextype/issues/502))

* **rest-api** fix issue with Rest API endpoints initialisation. ([#506](https://github.com/flextype/flextype/issues/506))

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
        'update' => [
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
* **core** add Atomastic Components instead of Flextype Components ([#478](https://github.com/flextype/flextype/issues/478))

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

* **entries** Add ability to extend Core class with Macros. ([#489](https://github.com/flextype/flextype/issues/489))

* **cache** add new cache engine - PHPFastCache instead of Doctrine Cache ([#457](https://github.com/flextype/flextype/issues/457))

    #### New config for PhpFastCache
    https://github.com/flextype/flextype/blob/dev/src/flextype/settings.yaml#L127-L241  

    #### New methods from PhpFastCache
    We are start using PhpFastCache PSR16 adapter  
    https://github.com/PHPSocialNetwork/phpfastcache   


* **core** Unit Test powered by PestPHP.

* **media** add new `move()` method instead of `rename()`

* **entries** add new `move()` method instead of `rename()`

* **core** add new `PATH['tmp']` constant ([#470](https://github.com/flextype/flextype/issues/470))

    Now we have:

    `PATH['tmp']` constant instead of `PATH['cache']` and `PATH['logs']`

* **markdown** add markdown basic settings ([#471](https://github.com/flextype/flextype/issues/471))

    ```yaml
    markdown:
      auto_line_breaks: false
      auto_url_links: false
      escape_markup: false
    ```

* **markdown** add ability to access markdown parser instance ([#468](https://github.com/flextype/flextype/issues/468))

    Usage:

    ```php
    $markdown = flextype('markdown')->getInstance();
    ```

* **entries** add new Flextype Entries Memory Storage (Flextype EMS). New private property `$storage` for storing current requested entry(or entries) data and all Entries CRUD operations data in memory with ability to change them dynamically on fly. New public methods `getStorage()` `setStorage()` ([#467](https://github.com/flextype/flextype/issues/467))

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
        'update' => [
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
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->setStorage('fetch.data.title', 'New title');
    });

    $entry = flextype('entries')->fetchSingle('about');

    echo $entry['title'];
    ```

* **entries** add new events: `onEntryHas`, `onEntryInitialized`, `onEntriesInitialized` ([#467](https://github.com/flextype/flextype/issues/467))

* **helpers** add new support helper `find()` for files and directories searching instead of `find_filter()`

* **helpers** add new support helper `filter()` for data collection filtering instead of `arrays_filter()`

### Bug Fixes

* **entries** fix issue with `delete()` method ([#465](https://github.com/flextype/flextype/issues/465))

* **media** fix issue with `exif_read_data()` on files upload.

### Refactoring

* **entries** remove App from all core namespaces ([#469](https://github.com/flextype/flextype/issues/469))

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

* **core** use new `PATH['tmp']` constant instead of `PATH['cache']` and `PATH['logs']` ([#470](https://github.com/flextype/flextype/issues/470))

* **cache** old cache config removed, use new config for PhpFastCache ([#457](https://github.com/flextype/flextype/issues/457))

* **cache** use methods `has()` `set()` `get()` instead of `contains()` `save()` `fetch()` ([#457](https://github.com/flextype/flextype/issues/457))

* **core** remove flextype-components/session ([#473](https://github.com/flextype/flextype/issues/473))

* **core** remove flextype-components/cookie ([#473](https://github.com/flextype/flextype/issues/473))

* **core** remove flextype-components/number ([#474](https://github.com/flextype/flextype/issues/474))

* **core** remove flextype-components/filesystem ([#474](https://github.com/flextype/flextype/issues/474))

* **core** remove flextype-components/arrays ([#474](https://github.com/flextype/flextype/issues/474))


<a name="0.9.11"></a>
# [0.9.11](https://github.com/flextype/flextype/compare/v0.9.10...v0.9.11) (2020-08-25)

### Features

* New helper function added for access all Flextype features in one place

    ```php
    flextype($container_name = null, $container = [])
    ```

    **IMPORTANT**

    Do not use `$flextype` object to access Flextype features, use `flextype()` helper function.

### Bug Fixes

* **core** fix bug - Cannot access protected property Flextype\App\Foundation\Flextype::$container ([#462](https://github.com/flextype/flextype/issues/462))
* **core** fix bug - Cannot use object of type Flextype\App\Foundation\Flextype as array ([#461](https://github.com/flextype/flextype/issues/461))
* **media** fix Media exif_read_data warning - File not supported ([#464](https://github.com/flextype/flextype/issues/464))

### Refactoring

* **plugins** remove $flextype variable from plugins init method.
* **entries** update return type for fetch() method.
* **entries** add additional check for getTimestamp() method in the getCacheID()
* **entries** remove dead code from fetchCollection() method.

### Vendor Updates

* **core:** Update vendor flextype-components/filesystem to 2.0.8
* **core:** Update vendor ramsey/uuid to 4.1.1

<a name="0.9.10"></a>
# [0.9.10](https://github.com/flextype/flextype/compare/v0.9.9...v0.9.10) (2020-08-19)

### Features

* **core** Moving to PHP 7.3.0 ([#456](https://github.com/flextype/flextype/issues/456))
* **core** add new class `Flextype` that extends `Slim\App` ([#458](https://github.com/flextype/flextype/issues/458))

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

* **collection** Add `only()` method for Collection ([#455](https://github.com/flextype/flextype/issues/455))

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

* **shortcode** add New Shortcode ([#454](https://github.com/flextype/flextype/issues/454))

    ```
    [raw] Raw shortcode content [/raw]
    ```

* **shortcode** add New Shortcode Methods ([#454](https://github.com/flextype/flextype/issues/454))

    ```
    // Get shortcode instance.
    getInstance()

    // Add shortcode handler.
    addHandler(string $name, $handler)

    // Add event handler.
    addEventHandler($name, $handler)

    // Processes text and replaces shortcodes.
    process(string $input, bool $cache = true)
    ```

### Bug Fixes

* **entries** fix issue with entries paths on Windows ([#460](https://github.com/flextype/flextype/issues/460))
* **cache** fix issue with `purge()` method. ([#451](https://github.com/flextype/flextype/issues/451))
* **entries** fix wrong Implementation of Slug Field for Entries ([#452](https://github.com/flextype/flextype/issues/452))
* **entries** add new entry field `id` ([#452](https://github.com/flextype/flextype/issues/452))

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

* **entries** fix wrong Implementation of Slug Field for Entries ([#452](https://github.com/flextype/flextype/issues/452))

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
* **core** Add PhpArrayFileAdapter and set PhpArrayFile Cache as a default fallback cache driver instead of Filesystem Cache driver. This new feature give us performance boost up to 25%
* **core** Add preflight to Flextype basic checks and performance boost.
* **core** Update all namespaces and core infrastructure. #437
* **core** Add Symfony Finder Component and `find_filter()` helper.
* **cache** Cache API improvements

    * Cache ID generation enhancements
    * add new public function `fetchMultiple(array $keys)`
    * add new public function `saveMultiple(array $keysAndValues, $lifetime = 0)`
    * add new public function `deleteMultiple(array $keys)`
    * add new public function `deleteAll()`
    * add new public function `flushAll()`
    * add new public function `purge(string $directory)`
    * add new public function `purgeAll()`
    * add new public function `getStats()`
    * add new events `onCacheBeforePurgeAll`, `onCacheAfterPurgeAll`, `onCacheBeforePurge`, `onCacheAfterPurge`

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

    $flextype->shortcode->add(string $name, $handler)
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

* **entries** New events added for Entries API.

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

* **entries** New decoupled and configurable fields added for entries instead of hardcoded.

    Entry fields decoupled into: `/flextype/Foundation/Entries/Fields/`

    Entry fields added into `flextype.settings.entries.fields`

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

* **entries** Add ability to set individual cache control for specific entries.

    ```
    cache:
      enabled: true

    or

    cache:
      enabled: false
    ```

* **entries** Add new Entries API class properties.

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

* **frontmatter** remove UTF-8 BOM if it exists.
* **frontmatter** fix line endings to Unix style.
* **entries** fix method `rename()` in Entries API #433
* **entries** fix issue with parsing content on entry fetch #441
* **rest-api** fix Rest API JSON Response #445
* **core** fix all namespaces #437
* **core** fix flextype config loading.
* **serializers** fix YAML native parser.
* **plugins** fix method `getPluginsCacheID()` for Plugins API

### Refactoring
* **pimple** remove unused $flextype variable and cleanup dependencies.
* **yaml** save and mute error_reporting for native YAML parser.
* **cors** remove unused parameter $args
* **plugins**  remove dead variables.
* **shortcode** update return type for shortcode add() method.
* **cache** update $driver type for DoctrineCache.

### Vendor Updates

* **core:** Update vendor league/glide to 1.6.0
* **core:** Update vendor doctrine/cache to 1.10.2
* **core:** Update vendor doctrine/collections to 1.6.6
* **core:** Update vendor respect/validation to 2.0.16
* **core:** Update vendor monolog/monolog to 2.1.1
* **core:** Update vendor thunderer/shortcode to 0.7.4
* **core:** Update vendor flextype-components/filesystem to 2.0.7
* **core:** Update vendor flextype-components/registry to 3.0.0
* **core:** Update vendor flextype-components/number to 1.1.1
* **core:** Update vendor composer/semver to 3.0.0
* **core:** Update vendor symfony/yaml to 5.1.3
* **core:** Update vendor ramsey/uuid to 4.1.0

### BREAKING CHANGES

* **entries** Wildcard * removed from parsers field.
* **entries** Cache setup removed from parsers field.
* **settings** `/project/config/settings.yaml` move to `/project/config/flextype/settings.yaml`
* **constants** remove constant `PATH['config']`, use - `PATH['project'] . '/config/'`
* **core:** remove Date Component from the system.
* **core:** remove Text Component from the system.
* **cache:** removed methods clear() and clearAll(), use purge() and purgeAll() instead.
* **cache:** change return type for methods `save()`, `delete()` from void too bool.

<a name="0.9.8"></a>
# [0.9.8](https://github.com/flextype/flextype/compare/v0.9.7...v0.9.8) (2020-05-14)

### Features
* **core:** New lightweight and powerful core for kickass Applications!
* **core:** New Content Management API (CMA) for Entries. #421

    The Content Management API (CMA), is a read-write API for managing content.

    You could use the CMA for several use cases, such as:

    * Automatic imports from WordPress, Joomla, Drupal, and more.
    * Integration with other backend systems, such as an e-commerce shop.
    * Building custom editing experiences.

    Endpoints for Content Management API:
    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/management/entries | Fetch entry(entries) |
    | POST | /api/management/entries | Create entry |
    | PATCH | /api/management/entries | Update entry |
    | PUT | /api/management/entries | Rename entry |
    | PUT | /api/management/entries/copy | Copy entry(entries) |
    | DELETE | /api/management/entries | Delete entry |

    API Tokens folder: /project/tokens/management/entries

* **core:** New Images API.

    | Method | Endpoint | Description |
    | --- | --- | --- |
    | GET | /api/images | Fetch image |

    API Tokens folder: /project/tokens/images

* **core:** New Access API to create secret tokens for Content Management API (CMA).

    API Tokens folder: /project/tokens/access

* **core:** add Container for extending Flextype Container instead of Controller(s)
* **core:** add Application URL `url` into the common Flextype settings #405
* **core:** add new improved plugins sorting in the Plugins API.
* **core:** add dependencies validation for Plugins API #411
* **core:** add configurable CORS (Cross-origin resource sharing).

    ```
    cors:
      enabled: true
      origin: "*"
      headers: ["X-Requested-With", "Content-Type", "Accept", "Origin", "Authorization"]
      methods: [GET, POST, PUT, DELETE, PATCH, OPTIONS]
      expose: []
      credentials: false
    ```

* **core:** add manifest file `/src/flextype/config/flextype.yaml` for Flextype.
* **core:** add Serializer for data encoding/decoding and Parser for data parsing #424

### Bug Fixes

* **core:** fix incorrect data merging of manifest and settings for plugins and themes #404

### BREAKING CHANGES

* **core:** core decoupled in the plugins, and moved out of the Flextype release package!

    Install all needed plugins for your project by your self.
    Browse plugins: https://github.com/flextype-plugins

* **core:** new way for data merging of manifest and settings for plugins and themes #404

    for e.g. this is a wrong code to access site title:
    ```
    {{ registry.plugins.site.title|e('html') }}
    ```

    and this is a correct code to access site title:
    ```
    {{ registry.get('plugins.site.settings.title')|e('html') }}
    ```
* **core:** We should add app `url` into the core instead of `base_url` and `site_url` #405

    for e.g. this is a wrong code to access site url:
    ```
    {{ registry.plugins.site.url }}
    ```

    and this is a correct code to access app url:
    ```
    {{ registry.get('flextype.settings.url') }}
    ```

* **core:** new `project` folder instead of `site`

    - rename folder `site` into `project`
    - use new constant PATH['project'] instead of constant PATH['site']

* **core:** removed constants

    - PATH['plugins']
    - PATH['themes']
    - PATH['entries']
    - PATH['themes']
    - PATH['snippets']
    - PATH['fieldsets']
    - PATH['tokens']
    - PATH['accounts']
    - PATH['uploads']

* **core:** removed Snippets functionality

### Update from Flextype 0.9.7 to Flextype 0.9.8

1. Backup your Site First!
2. Read BREAKING CHANGES section!
3. Download flextype-0.9.8.zip
4. Unzip the contents to a new folder on your local computer.
5. Remove on your server this folders and files:
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
9. Create CDA, CMA and Access tokens for your project using this [webpage](https://flextype.org/en/api-token-generator).

<a name="0.9.7"></a>
# [0.9.7](https://github.com/flextype/flextype/compare/v0.9.6...v0.9.7) (2020-03-03)

### Features
* **core:** add Delivery API's for Entries, Images and Registry. #159

    Use Flextype as a Headless CMS with the full power of the Admin Panel.
    Build a Websites and Apps with a technology you are familiar with.

    Endpoints for Delivery API's:
    ```
    /api/delivery/entries
    /api/delivery/images
    /api/delivery/registry
    ```

* **core:** add new core constants `PATH['tokens']`, `PATH['accounts']`, `PATH['logs']`, `PATH['uploads']`
* **core:** add new locales support Persian, Indonesian, Galician #327
* **core:** add alternative comparison syntax for Entries API  

    Alternative comparison syntax:
    ```
    != - Not equals to
    like - Contains the substring
    ```

* **core:** set entries field `routable`=`true` on new entry creation #320
* **core:** use `array_merge()` instead of `array_replace_recursive()` for entries update method.
* **core:** initialize plugins before themes #323
* **core:** update Cache to use adapter to retrieve driver object #341
* **core:** load Shortcodes extensions based on `flextype.shortcodes.extensions` array #352
* **core:** load Twig extensions based on flextype.twig.extensions array #351
* **core:** add new Global Vars `PATH_ACCOUNTS`, `PATH_UPLOADS`, `PATH_TOKENS`, `PATH_LOGS` for Twig.
* **default-theme:** Moving to Tailwind CSS from Twitter Bootstrap #356
* **site-plugin:** add ability to set custom site url, new shortcode `[site_url]` and twig var `{{ site_url }}`
* **form-plugin:** add new Form plugin for forms handling instead of core Forms API.
* **icon-plugin:** add new Icon plugin for Font Awesome icons set.

    usage in templates:
    ```
    <i class="icon">{{ icon('fab fa-apple') }}</i>
    ```

    usage in entries content:
    ```
    [icon value="fab fa-apple"]
    ```

* **(site-plugin):** add ability to access `uri` variable in the theme templates.

    usage in templates:
    ```
    {{ uri }}
    ```

* **admin-plugin:** add RTL support for URLs #62

    /site/config/plugins/admin/settings.yaml
    ```
    ...
    entries:
      slugify: true # set `false` to disable slugify for entries
    ```

* **admin-plugin:** add ability to deactivate/activate all type of plugins. #211
* **admin-plugin:** add Confirmation Required modal for system plugins deactivation.
* **admin-plugin:** new Admin Panel UI with better UX and powered by Tailwind CSS.
* **admin-plugin:** new improved entries media manager page.
* **admin-plugin:** add ability to continue editing after saving in the editor.
* **admin-plugin:** add action `onAdminThemeTail` for admin panel `base` layout.
* **admin-plugin:** add ability to change entries view from `list view` to `table view`.

    /site/config/plugins/admin/settings.yaml
    ```
    ...
    entries:
      items_view_default: list # set `table` for table entries view
    ```

* **admin-plugin:** increase upload limit for `_uploadFile` from 3mb to 5mb
* **admin-plugin:** do not rewrite plugins and themes manifest with custom manifests.
* **admin-plugin:** add parsleys for frontend form validation.
* **admin-plugin:** add select2 for all select form controls.
* **admin-plugin:** add swal for all modals.
* **admin-plugin:** add flatpickr for date and time.
* **admin-plugin:** add tippy.js for all tooltips and dropdown menus.
* **admin-plugin:** add confirmation modals powered by swal for all critical actions.
* **admin-plugin:** add dim color for entries with `draft`, `hidden` and `routable`=`false` status #324
* **admin-plugin:** add ability to select entry type in the nice modal on entry creation. #331
* **admin-plugin:** add new setting `entries.items_view_default` with default value `list`.
* **admin-plugin:** add ability for redirect to the editor after creating #343
* **admin-plugin:** add ability to create default API tokens on installation process.
* **admin-plugin:** add ability to use local SVG version of Font Awesome Icons #322

    usage in templates:
    ```
    <i class="icon">{{ icon('fas fa-ellipsis-h') }}</i>
    ```

### Bug Fixes

* **core:** fix discord server link #325
* **core:** fix issue with system fields data types in the Entries API #383
* **admin-plugin:** fix issue for creating entry process with same IDs #333
* **admin-plugin:** fix redirect for entries after edit process.
* **admin-plugin:** fix issues with routable field on entry edit process.

### Refactoring

* **core:** move `/site/cache directory` to the `/var/cache` #347
* **core:** remove Forms API from Flextype core #360
* **admin-plugin:** improve Gulp configuration for better assets building.
* **default-theme:** improve Gulp configuration for better assets building.
* **core:** simplify logic for themes initialization process, remove extra checks for theme setting is `enabled` or not.
* **admin-plugin:** move templates from `views` folder into the `templates` folder #347
* **admin-plugin:** remove unused namespaces in EntriesContoller #347
* **admin-plugin:** remove complex logic for themes activation process.
* **admin-plugin:** add `ext-gd` to the require section of composer.json #347
* **admin-plugin:** add `ext-fileinfo` to the require section of composer.json #347
* **admin-plugin:** add `ext-dom` to the require section of composer.json #347
* **admin-plugin:** add `ext-spl` to the require section of composer.json #347
* **default-theme:** remove `enabled` option from theme settings.

### Vendor Updates
* **core:** Update vendor monolog/monolog to 2.0.2
* **core:** Update vendor cocur/slugify to 4.0.0
* **core:** Update vendor thunderer/shortcode to 0.7.3
* **core:** Update vendor ramsey/uuid to 3.9.2

### BREAKING CHANGES

* **core:** accounts moved to their specific folders.

    for e.g.
    ```
    /accounts/admin.yaml => /accounts/admin/profile.yaml
    ```

* **core:** remove Debug, Html and Form Flextype Components.
* **core:** all images links should be updated
    ```
    http://docs.flextype.org/en/content/media
    ```
* **core:** core and plugin settings keys renamed
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

* **admin-plugin:** remove Twitter Bootstrap from Admin Panel and Default Theme.
* **admin-plugin:** remove user profile page `/admin/profile`
* **admin-plugin:** method `getUsers()` renamed to `getUsersList()` in UsersController.

<a name="0.9.6"></a>
# [0.9.6](https://github.com/flextype/flextype/compare/v0.9.5...v0.9.6) (2019-12-01)

### Features

* **core:** add ability to hide title for hidden fields #240
* **core:** add new public method delete() for Cache #308
* **core:** add CacheTwigExtension #309  

    usage in templates:
    ```
    {{ cache.CACHE_PUBLIC_METHOD }}
    ```

* **core:** add ability to override plugins default manifest and settings #224
* **core:** add ability to override themes default manifest and settings #256
* **core:** add ability to set help text for generated form controls #283  

    usage in fieldsets:
    ```
    help: "Help text here"
    ```

* **core:** add ability to store entry system fields in entries create method #247
* **core:** add alternative comparison syntax for Entries API  

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

* **core:** add `json_encode` and `json_decode` twig filter #289  

    usage in templates:
    ```
    // Result: {"title": "Hello World!"}
    {{ {'title': 'Hello World!'}|json_encode }}

    // Result: Hello World!
    {{ '{"title": "Hello World!"}'|json_decode.title }}
    ```

* **core:** add parser twig extension #262
* **core:** add new field property `default` instead of `value` #303
* **core:** add `yaml_encode` and `yaml_decode` twig filter #290  

    usage in templates:
    ```
    // Result: title: 'Hello World!'
    {{ {'title': 'Hello World!'}|yaml_encode }}

    // Result: Hello World!
    {{ 'title: Hello World!'|yaml_decode.title }}
    ```

* **core:** Markdown parsing should be cached in production #287
* **core:** YAML parsing will be cached in production #263
* **core:** Refactor entries fetch methods naming #315  

    we have:  
    `fetch` - for single and collection entries request  
    `fetchSingle` - for single entry request.   
    `fetchCollection` - for collection entries request.  

* **core:** add routable option for entries #284  

    usage in entry:
    ```
    routable: false
    ```
    by default `routable` is `true`

* **admin-plugin:** add help text for common form controls #280
* **admin-plugin:** add icons for settings tabs sections #293
* **admin-plugin:** hide textarea control for codemirror editor #279
* **admin-plugin:** show themes title instead of themes id's on settings page #187
* **admin-plugin:** add ability to set individual icons #250
* **admin-plugin:** add ability to set individual icons for plugins #255
* **admin-plugin:** add ability to work with entry custom fieldset #246
* **admin-plugin:** add individual icons for snippets #253
* **admin-plugin:** add individual icons for templates and partials #254
* **admin-plugin:** add plugins settings page #258
* **admin-plugin:** add themes settings page #296
* **admin-plugin:** show message on plugins page if no plugins installed #294
* **admin-plugin:** use dots icon for actions dropdown #292
* **admin-plugin:** add auto generated slugs from title field #305
* **admin-plugin:** add help tooltips #306
* **admin-plugin:** store Entires/Collections counter in cache #203
* **admin-plugin:** YAML parsing will be cached in production #263
* **admin-plugin:** add ability to hide fieldsets from entries type select #304  

    usage in fieldsets:
    ```
    hide: true
    ```
    by default `hide` is `false`

* **site-plugin:** add routable option for entries #284  


### Performance Improvements

* **core:** add realpath_cache_size to .htaccess
* **core:** improve Plugins API - locales loading and increase app performance #259
* **core:** improve Cache on production and increase app performance #290 #263  


### Bug Fixes

* **admin-plugin:** fix issue with saving entry source #251
* **admin-plugin:** fix file browser styles
* **admin-plugin:** fix breadcrumbs for theme templates
* **core:** Entries API - fix Collection Undefined Index(s) for fetchAll method #243
* **core:** fix broken logic for form inputs without labels #274
* **core:** fix default and site settings loading #297
* **core:** fix id's names for all generated fields #277
* **core:** fix notice undefined index: created_at in Entries API
* **core:** fix notice undefined index: published_at in Entries API #265
* **core:** fix Plugins API - createPluginsDictionary method and increase app perfomance #259
* **core:** fix state of active tabs for all generated forms #276
* **core:** fix state of aria-selected for all generated forms #275  


### Vendor Updates
* **core:** Update vendor flextype-components/date to 1.0.0
* **core:** Update vendor symfony/yaml to 4.4.0
* **core:** Update vendor doctrine/cache to 1.10.0
* **core:** Update vendor doctrine/collections to 1.6.4
* **core:** Update vendor monolog/monolog to 3.12.3
* **core:** Update vendor bootstrap to 4.4.1
* **admin-plugin:** Update vendor bootstrap to 4.4.1
* **admin-plugin:** Update vendor trumbowyg to 2.20.0  


### BREAKING CHANGES

* **core:** method fetchAll removed! please use `fetch`, `fetchSingle` or `fetchCollection`
* **core:** changed and_where & or_where execution in the templates  

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

* **core:** Rename property `value` to `default` for all fieldsets where it is used.

<a name="0.9.5"></a>
# [0.9.5](https://github.com/flextype/flextype/compare/v0.9.4...v0.9.5) (2019-09-21)
### Bug Fixes

* **core:** issue with cache in the Entries API - fetchAll method #234 2779777
* **core:** issue with emitter twig function #234 426a073
* **core:** issue with empty entries folder Entries API - fetchAll method #234 cf61f2d
* **core:** issue with Cache ID for Themes list #234 594f4a3
* **admin-plugin:** issue with active button styles on Themes Manager page #234 434f336
* **admin-plugin:** issue with emitter twig function #234 806b18e
* **admin-plugin:** Russian translations #233
* **site-plugin:** notice for undefined $query['format'] #234 8bde8eb

### Code Refactoring
* **core:** remove $response from Forms render method #234
* **core:** add property forms to Flextype\EntriesController #234

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
* Flextype Core: Add ability to work with different types of content #212 #186
* Flextype Core: Add new filter `tr` for I18nTwigExtension #186
* Flextype Core: Add MARKDOWN, YAML and JSON parsers. #212 #186
* Flextype Core: Add YamlTwigExtension #186
* Flextype Core: Add ResponseTime Middleware #186
* Flextype Core: Add UUID (universally unique identifier) for all entries #197 #186
* Flextype Core: Add message for Glide if image not found #189 #186
* Flextype Core: Add victorjonsson/markdowndocs for generating markdown-formatted class documentation #186
* Flextype Core: Add custom callable resolver, which resolves PSR-15 middlewares. #213 #186
* Flextype Core: Add git commit message convention. #186
* Flextype Core: Add AuthMiddleware globally #201 #186
* Flextype Core: Add new twig options `debug` `charset` `cache` #186
* Flextype Core: Add new field `tags` #186
* Flextype Core: Add new field `datetimepicker` #186
* Flextype Core: Add block for all direct access to .md files in .htaccess #186
* Flextype Core: Add block access to specific file types for these user folders in .htaccess #186
* Flextype Core: Add new option date_display_format #186
* Flextype Admin Panel: Add Trumbowyg view html code #193 #186
* Flextype Admin Panel: Add tail section for base.html template #186
* Flextype Admin Panel: Add new event onAdminThemeFooter in base.html template #186
* Flextype Admin Panel: Add ability to set `published_at`, `created_at` for site entries #186
* Flextype Admin Panel: Add ability to set `created_by`, `published_by` for site entries #186
* Flextype Site Plugin: Add ability to get query params inside twig templates #186
* Flextype Site Plugin: Add ability to get entries in JSON Format #186
* Flextype Default Theme: Add ability to work with tags for default theme #186

### Fixed
* Flextype Core: Fix ShortcodesTwigExtension issue with null variables #186
* Flextype Core: Fix issue with bind_where expression for Entries fetchAll method #186
* Flextype Core: Fix issue with and_where expression for Entries fetchAll method #186
* Flextype Core: Fix issue with or_where expression for Entries fetchAll method #186
* Flextype Admin Panel: Fix dark theme for admin panel #186 #168

### Changed
* Flextype Core: Moving to PHP 7.2 #198 #186
* Flextype Core: JsonParserTwigExtension renamed to JsonTwigExtension #186
* Flextype Core: Twig json_parser_decode renamed to json_decode #186
* Flextype Core: Twig json_parser_encode renamed to json_encode #186
* Flextype Core: Default theme - update assets building process and GULP to 4.X.X #206 #186
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
* Flextype Core: Update .gitignore
* Flextype Core: Update copyrights information
* Flextype Core: Update vendor flextype-components/filesystem to 2.0.6
* Flextype Core: Update vendor flextype-components/date to 1.1.0
* Flextype Core: Update vendor zeuxisoo/slim-whoops to 0.6.5
* Flextype Core: Update vendor doctrine/collections to 1.6.2
* Flextype Core: Update vendor slim/slim to 3.12.2
* Flextype Core: Update vendor respect/validation to 1.1.31
* Flextype Core: Update vendor monolog/monolog to 2.0.0
* Flextype Core: Update vendor symfony/yaml to 4.3.4
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
* Flextype Core: Remove `date` field #196 #186
* Flextype Admin Panel: Remove save button on the media page #225 #186
* Flextype Admin Panel: Remove unused css code #186
* Flextype Admin Panel: Remove unused js code #186

<a name="0.9.3"></a>
# [0.9.3](https://github.com/flextype/flextype/compare/v0.9.2...v0.9.3) (2019-07-07)
### Fixed
* Flextype Core: Entries - issue with binding arguments inside method fetchAll() - fixed. #182
* Flextype Core: Entries - issue with possible boolean false result from Filesystem::getTimestamp() inside method fetchAll() - fixed. #182
* Flextype Core: Entries - issue with possible boolean false result from Filesystem::getTimestamp() inside method fetch() - fixed. #182
* Flextype Admin Panel: critical issue with possibility to register two admins! - fixed. #183 #182
* Flextype Admin Panel: Left Navigation - active state for Templates area - fixed. #182
* Flextype Default Theme: issue with `TypeError: undefined is not an object` for lightbox - fixed. #182
* Flextype Default Theme: fix thumbnail image for Default Theme #182

<a name="0.9.2"></a>
# [0.9.2](https://github.com/flextype/flextype/compare/v0.9.1...v0.9.2) (2019-07-06)
### Added
* Flextype Default Theme: pagination for blog entries added. #164 #165
* Flextype Default Theme: New templates for entry Gallery - added. #165
* Flextype Core: New Shortcode [registry_get] - added. #165
* Flextype Core: New entry Gallery - added. #165
* Flextype Core: New fieldsets for entry Gallery - added. #165
* Flextype Core: Doctrine Collections - added. #175 #165
* Flextype Core: GlobalVarsTwigExtension - new variable - `PHP_VERSION` - added. #165
* Flextype Core: FilesystemTwigExtension - new function `filesystem_get_files_list` added. #165
* Flextype Core: Snippets - new snippet `google-analytics` added. #165
* Flextype Core: Fieldsets Content - menu_item_target fixed. #165
* Flextype Admin Panel: Show nice message if there is no items for current area. #158 #165
* Flextype Admin Panel: Tools - added. #170 #165
* Flextype Admin Panel: Tools - Cache area added. #170 #165
* Flextype Admin Panel: Tools - Registry area added. #170 #165
* Flextype Admin Panel: Themes manager added. #171 #165
* Flextype Admin Panel: New Translates added. #165

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
* Flextype Admin Panel: fix all tabs state for Fieldsets, Snippets, Templates areas. #165
* Flextype Admin Panel: Entries - move functionality issues #179 #165

### Removed
* Flextype Admin Panel: Left Navigation - documentation link - removed #165

<a name="0.9.1"></a>
# [0.9.1](https://github.com/flextype/flextype/compare/v0.9.0...v0.9.1) (2019-06-18)
### Added
* Flextype Admin Panel: new setting `route` added to customize admin base route. #154
* Flextype Core: GlobalVarsTwigExtension - new global constant `PATH_FIELDSETS` added. #154
* Flextype Core: Entries API - public property `$entry` added. #154
* Flextype Core: Entries API - public property `$entries` added. #154
* Flextype Core: Entries API - new event `onEntryAfterInitialized` added. #154
* Flextype Core: Entries API - new event `onEntriesAfterInitialized` added. #154
* Flextype Core: Shortcodes - `EntriesShortcode` added. #154
* Flextype Core: Shortcodes - `BaseUrlShortcode` added. #154
* Flextype Core: Snippets - SnippetsTwigExtension: `snippets_exec()` added. #154
* Flextype Core: Snippets - `[snppets_fetch]` shortcode added. #154
* Flextype Core: Snippets - `_exec_snippet()` method added. #154
* Flextype Core: Snippets - `exec()` method added. #154
* Flextype Core: Snippets - added ability to access $flextype and $app inside snippets. #154
* Flextype Core: GlobalVarsTwigExtension `FLEXTYPE_VERSION` added. #154
* Flextype Site Plugin: public property `$entry` added. #154
* Flextype Site Plugin: new event `onSiteEntryAfterInitialized` added. #154

### Fixed
* Flextype Core: Entries API - `fetchALL()` issue with fetching entries recursively fixed. #154 #161

### Changed
* Flextype Site: code refactoring. #154
* Flextype Admin Panel: code refactoring. #154
* Flextype Core: Snippets - from now we will set prefix `bind_` for all variables. #154

### Removed
* Flextype Core: Entries API - remove unused Shortcodes code from method `fetch()` #162
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
* Flextype Core: composer.json ext-json and ext-mbstring added into require section.
* Flextype Core: composer.json suggest section added.
* Flextype Core: composer.json: apcu-autoloader added for APCu cache as a fallback for the class map.
* Flextype Site: New plugin Site added.
* Flextype Core: Respect Validation - The most awesome validation engine ever created for PHP - added.
* Flextype Admin Panel: New admin panel plugin based on Slim Framework.
* Flextype Admin Panel: Fieldset Sections(groups) added.
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
* Admin Panel: Settings Manager - ability to change admin panel theme - added.
* Admin Panel: Settings Manager - Select dropdown for cache driver - added.
* Flextype Core: Cache - new cache driver Array - added.
* Flextype Core: Cache - new cache driver SQLite3 - added.
* Flextype Core: Cache - new cache driver Zend - added.

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
* Admin Panel: ClipboardJS added!
* Admin Panel: Media Manager - Twitter Bootstrap File browser - added.
* Admin Panel: Snippets Manager: Embeded code info modal added.
* Admin Panel: Settings Manager - Select dropdown for default entry - added.
* Admin Panel: Settings Manager - Select dropdown for timezones - added.
* Admin Panel: Settings Manager - Select dropdown for themes - added.

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
* Flextype Core: Cache - ability to delete glide cache folder added.

### Changed
* Flextype Core: Thunderer Shortcode updated to 0.7.0 - over 10x performance and memory usage improvement!
* Flextype Core: Default settings updates.
* Flextype Core: Arr Components updated to 1.2.4.
* Flextype Core: Default theme - Twitter Bootstrap update to 4.2.1
* Admin Panel: Media Manager - uploader improvements
* Admin Panel: Menus Manager - menus name are clickable now.
* Admin Panel: Fieldsets Manager - fieldsets name are clickable now.
* Admin Panel: Templates Manager - templates and partials name are clickable now.
* Admin Panel: Snippets Manager - snippets name are clickable now.
* Admin Panel: Settings Manager - look and feel improvements.
* Admin Panel: Twitter Bootstrap update to 4.2.1

### Fixed
* Admin Panel: Snippets Manager - shortcode issue - fixed.
* Admin Panel: gulpfile - issue with duplicated codemirror - fixed.
* Admin Panel: Trumbowyg styles fixes.
* Admin Panel: Plugins Manager - issue with broken homepage url in the Info Modal - fixed.

<a name="0.8.0"></a>
# [0.8.0](https://github.com/flextype/flextype/compare/v0.7.4...v0.8.0) (2018-12-28)
### Added
* Flextype Core: To improve engine flexibility was decided to use entity name Entries/Entry instead of entity name Pages/Page.
* Flextype Core: New folder `/site/entries/` added.
* Flextype Core: New entry variable `base_url` added.
* Flextype Core: Snippets functionality added.
* Flextype Core: New constant PATH['snippets'] added for Snippets.
* Flextype Core: New folder `/site/snippets/` added.
* Flextype Core: Menus functionality added.
* Flextype Core: New folder `/site/menus/` added.
* Flextype Core: Fieldsets functionality added.
* Flextype Core: Fallback functionality for settings added.
* Flextype Core: New settings item `accept_file_types` added.
* Flextype Core: Common PHP Overrides added to .htaccess
* Flextype Core: Custom YamlParser with native support to increase system performance added.
* Flextype Core: Ability to get hidden entries for method getEntries() added.
* Flextype Core: New setting options `entries.error404` for error404 page added.
* Admin Panel: Fieldsets Manager added.
* Admin Panel: Menus Manager added.
* Admin Panel: Snippets Manager added.
* Admin Panel: Templates Manager added.
* Admin Panel: Entries Manager with nice one level tree view for pages list added.
* Admin Panel: Português locale added.
* Admin Panel: General - trumbowyg - table plugin added.
* Flextype new Default Theme with predefined Fieldsets and Entries templates added.

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
* Content: new frontMatterParser() - added
* Config: set error reporting - false
* Update theme simple according to the php template syntax guidelines
* Super heavy "imagine/imagine": "1.2.0" - removed
* Flextype Component - Errorhandler updated to 1.0.5

<a name="0.7.3"></a>
# [0.7.3](https://github.com/flextype/flextype/compare/v0.7.2...v0.7.3) (2018-12-13)
* Content: visibility hidden for pages - added
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
* Plugins: New method getLocales() added
* Content: processPage() - ability to ignore content parsing - added.

<a name="0.7.0"></a>
# [0.7.0](https://github.com/flextype/flextype/compare/v0.6.1...v0.7.0) (2018-11-16)
* Update Symfony YAML to 4.1.1
* Update Text Component to 1.1.0
* Update Session Component to 1.1.1
* Update Doctrine Cache to 1.8.0
* Update I18n Component to 1.1.0
* Update Token Component to 1.2.0
* Content: field 'published' changed to 'visibility'
* Plugins: from now no need to add plugin names manually to the site.yaml
* Plugins: added ability to load plugins settings.yaml file
* Plugins: from now plugins configurations stored in the plugin-name/settings.yaml file
* Add system.yaml config file and use it for system configurations
* Themes: added ability to load themes settings.yaml file
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
* Flextype: new method setSiteConfig() added
* Flextype: new method setErrorHandler() updates
* Flextype: new method setErrorHandler() added
* Content: new protected method initParsers()
* Content: Blocks functionality removed - use Block Plugin
* Content: Section shortcode removed - use Section plugin
* Content: Site Url shortcode removed - use Site Url plugin
* Content: Registry shotcode remobed - use Registry plugin
* Content: Prevents automatic linking of URLs for Markdown parser
* Content: Method registerDefaultShortcodes() removed

<a name="0.4.4"></a>
# [0.4.4](https://github.com/flextype/flextype/compare/v0.4.3...v0.4.4) (2018-05-29)
* Content: added ability to work with CONTENT SECTIONS with help of shortcodes [section] and [section_create]
* Content: getPage() method will only return data about requested page and will not insert them in global $page array.
* Content: events: onPageContentAfter and onPageContentRawAfter was removed from getPage(), use event onCurrentPageBeforeDisplayed instead.
* Site Config: new items added: robots and description
* Theme Simple: Using Assets Component for css and js
* Theme Simple: New head meta added: description, keywords, robots, generator
* Theme Simple: Meta charset getting from registry site.charset
* Theme Simple: Fixed issue with broken paths for JS
* Theme Simple: gulpfile: build process updated
* Theme Simple: package.json: added gulp-concat and gulp-sourcemaps

<a name="0.4.3"></a>
# [0.4.3](https://github.com/flextype/flextype/compare/v0.4.2...v0.4.3) (2018-05-28)
* Content: set text/html request headers for displayCurrentPage() method
* Content: processCurrentPage() method added
* Content: event names changed: onPageBeforeRender to onCurrentPageBeforeProcessed
* Content: event names changed: onPageAfterRender to onCurrentPageAfterProcessed
* robots.txt file was removed, use Robots plugin instead
* Code cleanup and refactoring #5

<a name="0.4.2"></a>
# [0.4.2](https://github.com/flextype/flextype/compare/v0.4.1...v0.4.2) (2018-05-22)
* Settings: cache.enabled is true from now
* Content: new methods added: initShortcodes() initMarkdown() markdown()
* Events: new events added: onMarkdownInitialized and onShortcodesInitialized

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
* New powerful Content class added for working with content instead of Pages, Shortcode, Markdown
* Content: new page field: `published` added
* Content: method for page blocks added
* Content: cache added for pages and blocks
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
* ErrorHandler added with errors logs.
* Code cleanup and refactoring #5

<a name="0.2.1"></a>
# [0.2.1](https://github.com/flextype/flextype/compare/v0.2.0...v0.2.1) (2018-03-26)
* date_format setting added to /site/config.site.yml
* Pages: Fixed bug with pages sort and slice in getPages() method
* Pages: Fixed bug with pages list for /pages folder
* Pages: Fixes for generating page url field
* Pages: Added ability to create date field automatically for pages if date field is not exists.
* Code cleanup and refactoring #5

<a name="0.2.0"></a>
# [0.2.0](https://github.com/flextype/flextype/compare/v0.1.0...v0.2.0) (2018-03-23)
* Thunderer Shortcode Framework - added
* Cache Flextype::VERSION for cache key - added
* flextype/boot/shortcodes.php	- removed
* flextype/boot/events.php - removed
* Code cleanup and refactoring #5

<a name="0.1.0"></a>
# [0.1.0](https://github.com/flextype/flextype) (2018-03-21)
* Initial Release
