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
     * @access private
     */
    private Arrays $registry;

    /**
     * Entries options.
     *
     * collections:
     *   pattern     - Entries collection pattern.
     *   filename    - Entries collection data filename.
     *   extension   - Entries collection data extension.
     *   serializer  - Entries collection data serializer.
     *   fields      - Array of fields for entries collection.
     *
     * @var array
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
        $this->loadCollectionsActions();
        $this->loadCollectionsFields();
    }

    /** 
     * Load Collections Actions
     *
     * @access public
     */
    private function loadCollectionsActions(): void
    {
        $actions = [];

        if (! isset($this->options['collections']) ||
            ! is_array($this->options['collections'])) {
            return;
        }

        foreach ($this->options['collections'] as $collection) {
            
            if (
                ! isset($collection['actions']) ||
                ! is_array($collection['actions']) ||
                count($collection['actions']) <= 0
            ) {
                continue;
            }

            foreach ($collection['actions'] as $action) {

                if (! isset($action['path'])) {
                    continue;
                }

                $actions[] = ROOT_DIR . $action['path'];
            }
        }

        $actions = arrays($actions)->unique()->toArray();

        foreach ($actions as $action) {
            if (filesystem()->file($action)->exists()) {
               
                include_once $action; 
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
        // Entry data
        $this->registry()->set('fetch.id', $id);
        $this->registry()->set('fetch.options', $options);
        $this->registry()->set('fetch.data', []);

        // Set collection options
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
                        ->replace(PATH['project'] . '/entries/', '')
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
        // Entry data
        $this->registry()->set('move.id', $id);
        $this->registry()->set('move.newID', $newID);

        // Set collection options
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
        // Entry data
        $this->registry()->set('update.id', $id);
        $this->registry()->set('update.data', $data);

        // Set collection options
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
        // Entry data
        $this->registry()->set('create.id', $id);
        $this->registry()->set('create.data', $data);

        // Set collection options
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
        // Entry data
        $this->registry()->set('delete.id', $id);

        // Set collection options
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
        // Entry data
        $this->registry()->set('copy.id', $id);
        $this->registry()->set('copy.newID', $newID);

        // Set collection options
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
        // Entry data
        $this->registry()->set('has.id', $id);

        // Set collection options
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
        // Set collection options
        $this->registry()->set('collection.options', $this->getCollectionOptions($id));

        return PATH['project'] . '/entries/' . $id . '/' . $this->registry()->get('collection.options.filename') . '.' . $this->registry()->get('collection.options.extension');
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
        return PATH['project'] . '/entries/' . $id;
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
        if (registry()->get('flextype.settings.cache.enabled') === false) {
            return '';
        }
    
        $entryFile = $this->getFileLocation($id);

        if (filesystem()->file($entryFile)->exists()) {
            return strings('entry' . $entryFile . (filesystem()->file($entryFile)->lastModified() ?: ''))->hash()->toString();
        }

        return strings('entry' . $entryFile)->hash()->toString();
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
    public function setRegistry(array $registry = [])
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
