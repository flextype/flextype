<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Entries;

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;

use function array_merge;
use function arrays;
use function cache;
use function count;
use function emitter;
use function file_exists;
use function filesystem;
use function filter;
use function find;
use function is_array;
use function registry;
use function serializers;
use function strings;

class Entries
{
    use Macroable;

    /**
     * Entries Registry.
     *
     * Local entries registry used for storing current requested
     * entries data and allow to change them on fly.
     *
     * @var Arrays
     * 
     * @access private
     */
    private Arrays $registry;

    /**
     * Entries options.
     *
     * directory:      - string - Entries directory.
     * collections:    - array  - Array of entries collections.
     *   name:         - string - Unique name of entries collection.
     *     pattern:    - string - Entries collection pattern.
     *     filename:   - string - Entries collection data filename.
     *     extension:  - string - Entries collection data extension.
     *     serializer: - string - Entries collection data serializer.
     *     fields:     - array  - Array of fields for entries collection.
     *     events:     - array  - Array of events for entries collection.
     *
     * @var array
     * 
     * @access private
     */
    private array $options = [];

    /**
     * Create a new entries object.
     * 
     * @param array $options  Entries options.
     * @param array $registry Entries registry.
     */
    public function __construct(array $options = [], array $registry = [])
    {
        $this->setRegistry($registry);
        $this->setOptions($options);
        $this->loadCollectionsEvents();
        $this->loadCollectionsFields();
    }

    /** 
     * Load Collections Events
     *
     * @access public
     */
    private function loadCollectionsEvents(): void
    {
        $events = [];

        if (! isset($this->options['collections']) ||
            ! is_array($this->options['collections'])) {
            return;
        }

        foreach ($this->options['collections'] as $collection) {
            
            if (
                ! isset($collection['events']) ||
                ! is_array($collection['events']) ||
                count($collection['events']) <= 0
            ) {
                continue;
            }

            foreach ($collection['events'] as $event) {

                if (! isset($event['path'])) {
                    continue;
                }

                $events[] = ROOT_DIR . $event['path'];
            }
        }

        $events = arrays($events)->unique()->toArray();

        foreach ($events as $event) {
            if (filesystem()->file($event)->exists()) {
               
                include_once $event; 
            }
        } 
    }

    /** 
     * Load Collections Fields
     *
     * @access public
     */
    private function loadCollectionsFields(): void
    {
        $fields = [];

        if (! isset($this->options['collections']) ||
            ! is_array($this->options['collections'])) {
            return;
        }

        foreach ($this->options['collections'] as $collection) {
            if (
                ! isset($collection['fields']) ||
                ! is_array($collection['fields']) ||
                count($collection['fields']) <= 0
            ) {
                continue;
            }

            foreach ($collection['fields'] as $field) {

                if (! isset($field['path'])) {
                    continue;
                }

                $fields[] = ROOT_DIR . $field['path'];
            }
        }

        $fields = arrays($fields)->unique()->toArray();

        foreach ($fields as $field) {
            if (filesystem()->file($field)->exists()) {
                include_once $field; 
            }
        } 
    }

    /**
     * Get Entries Collection Options.
     * 
     * @param string $id Unique identifier of the entry.
     *
     * @return array Returns array of collection options.
     * 
     * @access public
     */
    private function getCollectionOptions(string $id): array
    {      
        $result = $this->options['collections']['default'];

        foreach ($this->options['collections'] as $collection) {
            if (isset($collection['pattern'])) {
                if (boolval(preg_match_all('#^' . $collection['pattern'] . '$#', $id, $matches, PREG_OFFSET_CAPTURE))) {
                    $result = $collection;
                }
            }
        }

        return $result;
    }

    /**
     * Fetch.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @return mixed Returns mixed results from APIs or default is an instance of The Arrays class with founded items.
     *
     * @access public
     */
    public function fetch(string $id, array $options = [])
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('fetch.id', $id)
                ->set('fetch.options', $options)
                ->set('fetch.result', null);
  
        // Run event
        emitter()->emit('onEntriesFetch');

        // Check if `fetch.result` contains data to return.
        if (! is_null($this->registry()->get('fetch.result'))) {
            return $this->registry()->get('fetch.result');
        }
        
        // Single fetch helper
        $single = function ($id, $options) {
            
            // Set initial data.
            $this->registry()
                    ->set('collection.options', $this->getCollectionOptions($id))
                    ->set('fetch.id', $id)
                    ->set('fetch.options', $options)
                    ->set('fetch.result', null);

            // Run event
            emitter()->emit('onEntriesFetchSingle');

            // Check if `fetch.result` contains data to return.
            if (! is_null($this->registry()->get('fetch.result'))) {
                return $this->registry()->get('fetch.result');
            }

            // Get Cache ID for current requested entry
            $entryCacheID = $this->getCacheID($this->registry()->get('fetch.id'));
            
            // 1. Try to get current requested entry from the cache.
            if (cache()->has($entryCacheID)) {
                
                // Fetch entry from cache and apply `filterCollection` filter for fetch result
                $this->registry()->set('fetch.result', 
                                       filterCollection(cache()->get($entryCacheID),
                                                        $this->registry()->get('fetch.options.filter', [])));

                // Run event
                emitter()->emit('onEntriesFetchSingleCacheHasResult');
                
                // Return result from the cache.
                return arrays($this->registry()->get('fetch.result'));
            }
            
            // 2. Try to get requested entry from the filesystem
            if ($this->has($this->registry()->get('fetch.id'))) {

                // Get entry file location
                $entryFile = $this->getFileLocation($this->registry()->get('fetch.id'));
                
                // Try to get requested entry from the filesystem
                $entryFileContent = filesystem()->file($entryFile)->get();

                if ($entryFileContent === false) {
                    // Run event
                    emitter()->emit('onEntriesFetchSingleNoResult');
                    return arrays($this->registry()->get('fetch.result'));
                }

                // Decode entry file content
                $this->registry()->set('fetch.result', serializers()->{$this->registry()->get('collection.options')['serializer']}()->decode($entryFileContent));

                // Run event
                emitter()->emit('onEntriesFetchSingleHasResult');
                
                // Apply `filterCollection` filter for fetch result
                $this->registry()->set('fetch.result', filterCollection($this->registry()->get('fetch.result'), $this->registry()->get('fetch.options.filter', [])));

                // Set cache state
                $cache = $this->registry()->get('fetch.result.cache.enabled',
                                               registry()->get('flextype.settings.cache.enabled'));

                // Save entry data to cache
                if ($cache) {
                    cache()->set($entryCacheID, $this->registry()->get('fetch.result'));
                }
                
                // Return entry fetch result
                return arrays($this->registry()->get('fetch.result'));
            }

            // Run event
            emitter()->emit('onEntriesFetchSingleNoResult');

            // Return entry fetch result
            return arrays($this->registry()->get('fetch.result'));
        };

        if ($this->registry()->has('fetch.options.collection') &&
            strings($this->registry()->get('fetch.options.collection'))->isTrue()) {

            // Set initial data.
            $this->registry()
                    ->set('collection.options', $this->getCollectionOptions($id))
                    ->set('fetch.id', $id)
                    ->set('fetch.options', $options)
                    ->set('fetch.result', null);

            // Run event
            emitter()->emit('onEntriesFetchCollection');

            // Check if `fetch.result` contains data to return.
            if (! is_null($this->registry()->get('fetch.result'))) {
                return $this->registry()->get('fetch.result');
            }

            // Determine if collection exists
            if (! $this->has($this->registry()->get('fetch.id'))) {

                // Run event
                emitter()->emit('onEntriesFetchCollectionNoResult');

                // Return entries array
                return arrays($this->registry()->get('fetch.result'));
            }

            // Find entries in the filesystem.
            $entries = find($this->getDirectoryLocation($this->registry()->get('fetch.id')),
                            $this->registry()->has('fetch.options.find') ?
                            $this->registry()->get('fetch.options.find') :
                            []);

            // Walk through entries results
            if ($entries->hasResults()) {

                $data = [];

                foreach ($entries as $currenEntry) {

                    $currentEntryID = strings($currenEntry->getPath())
                        ->replace('\\', '/')
                        ->replace(PATH['project'] . $this->options['directory'] . '/', '')
                        ->trim('/')
                        ->toString();

                    // Set collection options
                    $this->registry()->set('collection.options', $this->getCollectionOptions($currentEntryID));

                    if ($currenEntry->getType() !== 'file' || $currenEntry->getFilename() !== $this->registry()->get('collection.options.filename') . '.' . $this->registry()->get('collection.options.extension')) {
                        continue;
                    }

                    $data[$currentEntryID] = $single($currentEntryID, [])->toArray();
                }
     
                // Re-init data.
                $this->registry()
                    ->set('collection.options', $this->getCollectionOptions($id))
                    ->set('fetch.id', $id)
                    ->set('fetch.options', $options)
                    ->set('fetch.result', $data);
                    
                // Run event
                emitter()->emit('onEntriesFetchCollectionHasResult');

                // Process filter `only` for collection
                // after process we need to unset $options['filter']['only']
                // to avoid it's running inside filterCollection() helper.
                if ($this->registry()->has('fetch.options.filter.only')) {
                    $data = [];
                    foreach ($this->registry()->get('fetch.result') as $key => $value) {
                        $data[$key] = arrays($value)->only($this->registry()->get('fetch.options.filter.only'))->toArray();
                    }
                    $this->registry()->delete('fetch.options.filter.only');
                    $this->registry()->set('fetch.result', $data);
                }

                // Process filter `except` for collection
                // after process we need to unset $options['filter']['except']
                // to avoid it's running inside filterCollection() helper.
                if ($this->registry()->has('fetch.options.filter.except')) {
                    $data = [];
                    foreach ($this->registry()->get('fetch.result') as $key => $value) {
                        $data[$key] = arrays($value)->except($this->registry()->get('fetch.options.filter.except'))->toArray();
                    }
                    $this->registry()->delete('fetch.options.filter.except');
                    $this->registry()->set('fetch.result', $data);
                }

                // Apply filter `filterCollection` for fetch result data.
                $this->registry()->set('fetch.result', 
                                       filterCollection($this->registry()->get('fetch.result'), 
                                                        $this->registry()->has('fetch.options.filter') ? 
                                                        $this->registry()->get('fetch.options.filter') : []));
                
                return arrays($this->registry()->get('fetch.result'));

            } else {
                // Run event
                emitter()->emit('onEntriesFetchCollectionNoResult');   

                // Return entries array
                return arrays($this->registry()->get('fetch.result'));
            }

            // Return entries array
            return arrays($this->registry()->get('fetch.result'));
        }

        // Fetch single entry.
        return $single($id, $options);
    }

    /**
     * Move entry.
     *
     * @param string $id    Unique identifier of the entry.
     * @param string $newID New Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function move(string $id, string $newID): bool
    {  
        // Collections validation helper.
        // Check if collections are identical.
        $isValidCollections = function ($id, $newID) {
            $collectionForCurrentEntry = $this->getCollectionOptions($id); 
            $collectionForNewEntry     = $this->getCollectionOptions($newID); 

            $result = true;

            if (! isset($collectionForCurrentEntry['filename']) ||
                ! isset($collectionForCurrentEntry['extension']) ||
                ! isset($collectionForCurrentEntry['serializer']) ||
                ! isset($collectionForNewEntry['filename']) ||
                ! isset($collectionForNewEntry['extension']) ||
                ! isset($collectionForNewEntry['serializer'])) {
                $result = false;
            }

            if (($collectionForCurrentEntry['filename'] != $collectionForNewEntry['filename']) ||
                ($collectionForCurrentEntry['extension'] != $collectionForNewEntry['extension']) ||
                ($collectionForCurrentEntry['serializer'] != $collectionForNewEntry['serializer'])) {
                $result = false;
            }

            return $result;
        };

        if (! $isValidCollections($id, $newID)) {
            return false;
        }

        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('move.id', $id)
                ->set('move.newID', $newID)
                ->set('move.result', null);

        // Run event
        emitter()->emit('onEntriesMove');

        // Return result from registy `move.result` if it's value boolean.
        if (! is_null($this->registry()->get('move.result')) && is_bool($this->registry()->get('move.result'))) {
            return $this->registry()->get('move.result');
        }

        // Do move.
        if (! $this->has($this->registry()->get('move.newID'))) {
            return filesystem()
                        ->directory($this->getDirectoryLocation($this->registry()->get('move.id')))
                        ->move($this->getDirectoryLocation($this->registry()->get('move.newID')));
        }

        return false;
    }

    /**
     * Update entry.
     *
     * @param string $id   Unique identifier of the entry.
     * @param array  $data Data to update for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, array $data): bool
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('update.id', $id)
                ->set('update.data', $data)
                ->set('update.result', null);

        // Run event
        emitter()->emit('onEntriesUpdate');

        // Return result from registy `update.result` if it's value boolean.
        if (! is_null($this->registry()->get('update.result')) && is_bool($this->registry()->get('update.result'))) {
            return $this->registry()->get('update.result');
        }

        $entryFile = $this->getFileLocation($this->registry()->get('update.id'));

        if (filesystem()->file($entryFile)->exists()) {
            $body  = filesystem()->file($entryFile)->get();
            $entry = serializers()->{$this->registry()->get('collection.options')['serializer']}()->decode($body);

            return (bool) filesystem()->file($entryFile)->put(serializers()->{$this->registry()->get('collection.options')['serializer']}()->encode(array_merge($entry, $this->registry()->get('update.data'))));
        }

        return false;
    }

    /**
     * Create entry.
     *
     * @param string $id   Unique identifier of the entry.
     * @param array  $data Data to create for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data = []): bool
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('create.id', $id)
                ->set('create.data', $data)
                ->set('create.result', null);

        // Run event
        emitter()->emit('onEntriesCreate');

        // Return result from registy `create.result` if it's value boolean.
        if (! is_null($this->registry()->get('create.result')) && is_bool($this->registry()->get('create.result'))) {
            return $this->registry()->get('create.result');
        }

        // Create entry directory first if it is not exists
        $entryDirectory = $this->getDirectoryLocation($this->registry()->get('create.id'));

        if (
            ! filesystem()->directory($entryDirectory)->exists() &&
            ! filesystem()->directory($entryDirectory)->create()
        ) {
            return false;
        }

        // Create entry file
        $entryFile = $entryDirectory . '/' . $this->registry()->get('collection.options.filename') . '.' . $this->registry()->get('collection.options.extension');
        if (! filesystem()->file($entryFile)->exists()) {
            return (bool) filesystem()->file($entryFile)->put(serializers()->{$this->registry()->get('collection.options')['serializer']}()->encode($this->registry()->get('create.data')));
        }

        return false;
    }

    /**
     * Delete entry.
     *
     * @param string $id Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id): bool
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('delete.id', $id)
                ->set('delete.result', null);

        // Run event
        emitter()->emit('onEntriesDelete');

        // Return result from registy `delete.result` if it's value boolean.
        if (! is_null($this->registry()->get('delete.result')) && is_bool($this->registry()->get('delete.result'))) {
            return $this->registry()->get('delete.result');
        }

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->registry()->get('delete.id')))
                    ->delete();
    }

    /**
     * Copy entry.
     *
     * @param string $id    Unique identifier of the entry.
     * @param string $newID New Unique identifier of the entry.
     *
     * @return boolы True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): bool
    {  
        // Collections validation helper.
        // Check if collections are identical.
        $isValidCollections = function ($id, $newID) {
            $collectionForCurrentEntry = $this->getCollectionOptions($id); 
            $collectionForNewEntry     = $this->getCollectionOptions($newID); 

            $result = true;

            if (! isset($collectionForCurrentEntry['filename']) ||
                ! isset($collectionForCurrentEntry['extension']) ||
                ! isset($collectionForCurrentEntry['serializer']) ||
                ! isset($collectionForNewEntry['filename']) ||
                ! isset($collectionForNewEntry['extension']) ||
                ! isset($collectionForNewEntry['serializer'])) {
                $result = false;
            }

            if (($collectionForCurrentEntry['filename'] != $collectionForNewEntry['filename']) ||
                ($collectionForCurrentEntry['extension'] != $collectionForNewEntry['extension']) ||
                ($collectionForCurrentEntry['serializer'] != $collectionForNewEntry['serializer'])) {
                $result = false;
            }

            return $result;
        };

        if (! $isValidCollections($id, $newID)) {
            return false;
        }

        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('copy.id', $id)
                ->set('copy.newID', $newID)
                ->set('copy.result', null);

        // Run event
        emitter()->emit('onEntriesCopy');

        // Return result from registy `move.result` if it's value boolean.
        if (! is_null($this->registry()->get('copy.result')) && is_bool($this->registry()->get('copy.result'))) {
            return $this->registry()->get('copy.result');
        }

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->registry()->get('copy.id')))
                    ->copy($this->getDirectoryLocation($this->registry()->get('copy.newID')));
    }

    /**
     * Check whether entry exists.
     *
     * @param string $id Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id): bool
    {   
        // Set initial data.
        $this->registry()
            ->set('collection.options', $this->getCollectionOptions($id))
            ->set('has.id', $id)
            ->set('has.result', null);

        // Run event
        emitter()->emit('onEntriesHas');

        // Return result from registy `has.result` if it's value boolean.
        if (! is_null($this->registry()->get('has.result')) && is_bool($this->registry()->get('has.result'))) {
            return $this->registry()->get('has.result');
        }

        return filesystem()->file($this->getFileLocation($this->registry()->get('has.id')))->exists();
    }

    /**
     * Get entry file location.
     *
     * @param string $id Unique identifier of the entry.
     *
     * @return string Entry file location.
     *
     * @access public
     */
    public function getFileLocation(string $id): string
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('getFileLocation.id', $id)
                ->set('getFileLocation.result', null);

        // Run event
        emitter()->emit('onEntriesGetFileLocation');

        // Return result from registy `getFileLocation.result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('getFileLocation.result')) && is_string($this->registry()->get('getFileLocation.result'))) {
            return $this->registry()->get('getFileLocation.result');
        }

        return PATH['project'] . $this->options['directory'] . '/' . $this->registry()->get('getFileLocation.id') . '/' . $this->registry()->get('collection.options.filename') . '.' . $this->registry()->get('collection.options.extension');
    }

    /**
     * Get entry directory location.
     *
     * @param string $id Unique identifier of the entry.
     *
     * @return string Entry directory location.
     *
     * @access public
     */
    public function getDirectoryLocation(string $id): string
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('getDirectoryLocation.id', $id)
                ->set('getDirectoryLocation.result', null);

        // Run event
        emitter()->emit('onEntriesGetDirectoryLocation');

        // Return result from registy `getDirectoryLocation.result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('getDirectoryLocation.result')) && is_string($this->registry()->get('getDirectoryLocation.result'))) {
            return $this->registry()->get('getDirectoryLocation.result');
        }

        return PATH['project'] . $this->options['directory'] . '/' . $this->registry()->get('getDirectoryLocation.id');
    }

    /**
     * Get Cache ID for entry.
     *
     * @param  string $id Unique identifier of the entry.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $id): string
    {
        // Set initial data.
        $this->registry()
                ->set('collection.options', $this->getCollectionOptions($id))
                ->set('getCacheID.id', $id)
                ->set('getCacheID.result', null);

        // Run event
        emitter()->emit('onEntriesGetCacheID');

        // Return result from registy `getCacheID.result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('getCacheID.result')) && is_string($this->registry()->get('getCacheID.result'))) {
            return $this->registry()->get('getCacheID.result');
        }
   
        if (registry()->get('flextype.settings.cache.enabled') === false) {
            return '';
        }
    
        $entryFile = $this->getFileLocation($this->registry()->get('getCacheID.id'));

        if (filesystem()->file($entryFile)->exists()) {
            return strings($this->options['directory'] . $entryFile . (filesystem()->file($entryFile)->lastModified() ?: ''))->hash()->toString();
        }

        return strings($this->options['directory'] . $entryFile)->hash()->toString();
    }

    /**
     * Get Entries Registry.
     *
     * @return Arrays Returns entries registry.
     *
     * @access public
     */
    public function registry(): Arrays
    {
        return $this->registry;
    }

    /**
     * Set Entries registry.
     *
     * @return void
     *
     * @access public
     */
    public function setRegistry(array $registry = []): void 
    {
        $this->registry = arrays($registry);
    }

    /**
     * Set Entries options.
     *
     * @return void
     *
     * @access public
     */
    public function setOptions(array $options = []): void 
    {
        $this->options = $options;
    }

    /**
     * Get Entries options.
     *
     * @return array Returns entries options.
     *
     * @access public
     */
    public function getOptions(): array 
    {
        return $this->options;
    }
}
