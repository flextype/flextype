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
     * collections:
     *   pattern    - string - Entries collection pattern.
     *   filename   - string - Entries collection data filename.
     *   extension  - string - Entries collection data extension.
     *   serializer - string - Entries collection data serializer.
     *   fields     - array  - Array of fields for entries collection.
     *   events     - array  - Array of events for entries collection.
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
     * @return Arrays Returns instance of The Arrays class with items.
     *
     * @access public
     */
    public function fetch(string $id, array $options = []): Arrays
    {
        // Set registry initial data for this method.
        $this->registry()->set('fetch.id', $id);
        $this->registry()->set('fetch.options', $options);
        $this->registry()->set('fetch.data', []);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($id));

        // Run event
        emitter()->emit('onEntriesFetch');
   
        // Single fetch helper
        $single = function ($id, $options) {
            
            // Set collection options
            $this->registry()->set('collection.options', $this->getCollectionOptions($id));
            
            // Entry data
            $this->registry()->set('fetch.id', $id);
            $this->registry()->set('fetch.options', $options);
            $this->registry()->set('fetch.data', []);

            // Run event
            emitter()->emit('onEntriesFetchSingle');

            // Get Cache ID for current requested entry
            $entryCacheID = $this->getCacheID($this->registry()->get('fetch.id'));

            // 1. Try to get current requested entry from cache
            if (cache()->has($entryCacheID)) {
                
                // Fetch entry from cache and Apply filter for fetch data
                $this->registry()->set('fetch.data', filterCollection(cache()->get($entryCacheID),
                                                        $this->registry()->get('fetch.options.filter', [])));

                // Run event
                emitter()->emit('onEntriesFetchSingleCacheHasResult');
                
                // Return entry from cache
                return arrays($this->registry()->get('fetch.data'));
            }
            
            // 2. Try to get current requested entry from filesystem
            if ($this->has($this->registry()->get('fetch.id'))) {
                // Get entry file location
                $entryFile = $this->getFileLocation($this->registry()->get('fetch.id'));
                
                // Try to get requested entry from the filesystem
                $entryFileContent = filesystem()->file($entryFile)->get();

                if ($entryFileContent === false) {
                    // Run event
                    emitter()->emit('onEntriesFetchSingleNoResult');
                    return arrays($this->registry()->get('fetch.data'));
                }

                // Decode entry file content
                $this->registry()->set('fetch.data', serializers()->{$this->registry()->get('collection.options')['serializer']}()->decode($entryFileContent));

                // Run event
                emitter()->emit('onEntriesFetchSingleHasResult');
                
                // Apply filter for fetch data
                $this->registry()->set('fetch.data', filterCollection($this->registry()->get('fetch.data'), $this->registry()->get('fetch.options.filter', [])));

                // Set cache state
                $cache = $this->registry()->get('fetch.data.cache.enabled',
                                               registry()->get('flextype.settings.cache.enabled'));

                // Save entry data to cache
                if ($cache) {
                    cache()->set($entryCacheID, $this->registry()->get('fetch.data'));
                }
                
                // Return entry data
                return arrays($this->registry()->get('fetch.data'));
            }

            // Run event
            emitter()->emit('onEntriesFetchSingleNoResult');

            // Return empty array if entry is not founded
            return arrays($this->registry()->get('fetch.data'));
        };

        if (isset($this->registry['fetch']['options']['collection']) &&
            strings($this->registry['fetch']['options']['collection'])->isTrue()) {

            // Run event
            emitter()->emit('onEntriesFetchCollection');

            if (! $this->getDirectoryLocation($id)) {
                // Run event
                emitter()->emit('onEntriesFetchCollectionNoResult');

                // Return entries array
                return arrays($this->registry()->get('fetch.data'));
            }

            // Find entries in the filesystem
            $entries = find($this->getDirectoryLocation($id),
                                                        isset($options['find']) ?
                                                            $options['find'] :
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

                $this->registry()->set('fetch.data', $data);

                // Run event
                emitter()->emit('onEntriesFetchCollectionHasResult');

                // Process filter `only` for collection
                // after process we need to unset $options['filter']['only']
                // to avoid it's running inside filterCollection() helper.
                if (isset($options['filter']['only'])) {
                    $data = [];
                    foreach ($this->registry()->get('fetch.data') as $key => $value) {
                        $data[$key] = arrays($value)->only($options['filter']['only'])->toArray();
                    }
                    unset($options['filter']['only']);
                    $this->registry()->set('fetch.data', $data);
                }

                // Process filter `except` for collection
                // after process we need to unset $options['filter']['except']
                // to avoid it's running inside filterCollection() helper.
                if (isset($options['filter']['except'])) {
                    $data = [];
                    foreach ($this->registry()->get('fetch.data') as $key => $value) {
                        $data[$key] = arrays($value)->except($options['filter']['except'])->toArray();
                    }
                    unset($options['filter']['except']);
                    $this->registry()->set('fetch.data', $data);
                }

                // Apply filter for fetch data
                $this->registry()->set('fetch.data', filterCollection($this->registry()->get('fetch.data'), isset($options['filter']) ? $options['filter'] : []));
            } else {
                // Run event:
                emitter()->emit('onEntriesFetchCollectionNoResult');   

                // Return entries array
                return arrays($this->registry()->get('fetch.data'));
            }

            // Return entries array
            return arrays($this->registry()->get('fetch.data'));
        } else {
        
            return $single($this->registry()->get('fetch.id'),
                            $this->registry()->get('fetch.options'));
        }
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
        // Set registry initial data for this method.
        $this->registry()->set('move.id', $id);
        $this->registry()->set('move.newID', $newID);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('move.id')));
        
        // Run event
        emitter()->emit('onEntriesMove');

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
        // Set registry initial data for this method.
        $this->registry()->set('update.id', $id);
        $this->registry()->set('update.data', $data);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('update.id')));
        
        // Run event
        emitter()->emit('onEntriesUpdate');

        $entryFile = $this->getFileLocation($this->registry()->get('update.id'));

        if (filesystem()->file($entryFile)->exists()) {
            $body         = filesystem()->file($entryFile)->get();
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
        // Set registry initial data for this method.
        $this->registry()->set('create.id', $id);
        $this->registry()->set('create.data', $data);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('create.id')));
        
        // Run event
        emitter()->emit('onEntriesCreate');

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
        // Set registry initial data for this method.
        $this->registry()->set('delete.id', $id);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('delete.id')));
        
        // Run event
        emitter()->emit('onEntriesDelete');

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
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): ?bool
    {  
        // Set registry initial data for this method.
        $this->registry()->set('copy.id', $id);
        $this->registry()->set('copy.newID', $newID);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('copy.id')));

        // Run event
        emitter()->emit('onEntriesCopy');

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
        // Set registry initial data for this method.
        $this->registry()->set('has.id', $id);

        // Set registry current collection options.
        $this->registry()->set('collection.options', $this->getCollectionOptions($this->registry()->get('has.id')));

        // Run event:
        emitter()->emit('onEntriesHas');
        
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
        // Set registry initial data for this method.
        $this->registry()->set('getFileLocation.id', $id);
        
        // Set registry initial data for this method.
        $this->registry()->set('collection.options', $this->getCollectionOptions($id));

        // Run event:
        emitter()->emit('onEntriesGetFileLocation');
        
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
        // Set registry initial data for this method.
        $this->registry()->set('getDirectoryLocation.id', $id);
        
        // Set registry initial data for this method.
        $this->registry()->set('collection.options', $this->getCollectionOptions($id));

        // Run event:
        emitter()->emit('onEntriesGetDirectoryLocation');
        
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
        // Set registry initial data for this method.
        $this->registry()->set('getCacheID.id', $id);

        // Set registry initial data for this method.
        $this->registry()->set('collection.options', $this->getCollectionOptions($id));

        // Run event:
        emitter()->emit('onEntriesGetCacheID');

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
