<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Entries;

use Glowy\Arrays\Arrays as Collection;
use Glowy\Macroable\Macroable;

use function array_merge;
use function Glowy\Arrays\arrays;
use function Flextype\cache;
use function count;
use function Flextype\emitter;
use function file_exists;
use function Glowy\Filesystem\filesystem;
use function Flextype\find;
use function is_array;
use function Flextype\registry;
use function Flextype\serializers;
use function Glowy\Strings\strings;
use function Flextype\filterCollection;
use function Flextype\collection;
use function Flextype\expression;

class Entries
{
    use Macroable;

    /**
     * Entries Registry.
     *
     * Local entries registry used for storing current requested
     * entries data and allow to change them on fly.
     *
     * @var Collection
     * 
     * @access private
     */
    private Collection $registry;

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
     * @var Collection
     * 
     * @access private
     */
    private Collection $options;

    /**
     * Create a new entries object.
     * 
     * @param mixed $options  Entries options.
     * @param mixed $registry Entries registry.
     */
    public function __construct($options = null, $registry = null)
    {
        filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/' . registry()->get('flextype.settings.entries.directory'))->ensureExists(0755, true);

        $this->setRegistry($registry);
        $this->setOptions($options);
        $this->initExpressions(registry()->get('flextype.settings.entries.expressions'));
        $this->initDirectives(registry()->get('flextype.settings.entries.directives'));
        $this->initMacros(registry()->get('flextype.settings.entries.macros'));
        $this->loadCollectionsEvents();
        $this->loadCollectionsFields();
    }

    /**
     * Remove Collection Macros.
     * 
     * @param mixed $data Entry data.
     * 
     * @return mixed
     * 
     * @access private
     */
    private function removeSystemFields($data) 
    {
        if (is_array($data)) {
            if (boolval(arrays($data)->get('macros.debug', registry()->get('flextype.settings.entries.macros.debug'))) === false) {
                unset($data['macros']);
            }
            if (boolval(arrays($data)->get('vars.debug', registry()->get('flextype.settings.entries.vars.debug'))) === false) {
                unset($data['vars']);
            }
        }

        return $data;
    }

    /** 
     * Init Macros
     *
     * @param array $macros Macros to init.
     * 
     * @access public
     */
    public function initMacros(array $macros): void
    {
        foreach ($macros as $key => $value) {
            if ($key == 'debug') {
                continue;
            }
            
            if (filesystem()->file(FLEXTYPE_ROOT_DIR . '/' . $value['path'])->exists()) {
                include_once FLEXTYPE_ROOT_DIR . '/' . $value['path']; 
            }
        } 
    }

    /** 
     * Init Directives
     *
     * @param array $directives Directives to init.
     * 
     * @access public
     */
    public function initDirectives(array $directives): void
    {
        foreach ($directives as $key => $value) {
            if (filesystem()->file(FLEXTYPE_ROOT_DIR . '/' . $value['path'])->exists()) {
                include_once FLEXTYPE_ROOT_DIR . '/' . $value['path']; 
            }
        } 
    }

    /**
     * Init Expressions
     * 
     * @param array $expressions Expressions to init.
     * 
     * @return void
     */
    public function initExpressions(array $expressions): void
    {
        if (count($expressions) <= 0) {
            return;
        }

        foreach ($expressions as $expression) {
            if (! isset($expression['enabled'])) {
                continue;
            }

            if (! $expression['enabled']) {
                continue;
            }

            if (! strings($expression['class'])->endsWith('Expression')) {
                continue;
            }

            if (class_exists($expression['class'])) {
                expression()->registerProvider(new $expression['class']());
            }
        }
    }

    /** 
     * Load events for current collection.
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

                $events[] = FLEXTYPE_ROOT_DIR . '/' . $event['path'];
            }
        }

        $events = collection($events)->unique()->toArray();

        foreach ($events as $event) {
            if (filesystem()->file($event)->exists()) {
                include_once $event; 
            }
        } 
    }

    /** 
     * Load fields for current collection.
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

                $fields[] = FLEXTYPE_ROOT_DIR . '/' . $field['path'];
            }
        }

        $fields = collection($fields)->unique()->toArray();
        
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
        $fieldsToInclude = [];

        foreach ($this->options['collections'] as $collection) {
            if (isset($collection['fields']['_include']['collection'])) {
                $fieldsToInclude = $this->options['collections'][$collection['fields']['_include']['collection']]['fields'];

                if (isset($collection['fields']['_include']['except'])) {
                    $fieldsToInclude = collection($fieldsToInclude)->except($collection['fields']['_include']['except'])->toArray();
                }
            }

            if (isset($collection['pattern'])) {
                if (boolval(preg_match_all('#^' . $collection['pattern'] . '$#', $id, $matches, PREG_OFFSET_CAPTURE))) {
                    $result = $collection;
                    $result['fields'] = array_merge($fieldsToInclude, $result['fields'] ?? []);
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
     * @return mixed Returns mixed results from APIs or default is an instance of The Collection class with founded items.
     *
     * @access public
     */
    public function fetch(string $id, array $options = [])
    {
        // Setup registry.
        $this->registry()->set('methods.fetch', [
            'collection' => $this->getCollectionOptions($id),
            'params' => [
                'id' => $id,
                'options' => $options,
            ],
            'result' => null,
        ]);
  
        // Run event
        emitter()->emit('onEntriesFetch');

        // Check if `result` contains data to return then return existing result.
        if (! is_null($this->registry()->get('methods.fetch.result'))) {
            return $this->removeSystemFields($this->registry()->get('methods.fetch.result'));
        }
        
        // Fetch collection or single
        if ($this->registry()->has('methods.fetch.params.options.collection') &&
            strings($this->registry()->get('methods.fetch.params.options.collection'))->isTrue()) {
            return $this->fetchCollection($this->registry()->get('methods.fetch.params.id'), 
                                          $this->registry()->get('methods.fetch.params.options'));
        } else {
            return $this->fetchSingle($this->registry()->get('methods.fetch.params.id'), 
                                      $this->registry()->get('methods.fetch.params.options'));
        }
    }

    /**
     * Fetch single.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @return mixed Returns mixed results from APIs or default is an instance of The Collection class with founded items.
     *
     * @access public
     */
    private function fetchSingle(string $id, array $options = [])
    {
        // Setup registry.
        $this->registry()->set('methods.fetch', [
            'collection' => $this->getCollectionOptions($id),
            'params' => [
                'id' => $id,
                'options' => $options,
            ],
            'result' => null,
        ]);

        // Run event
        emitter()->emit('onEntriesFetchSingle');

        // Check if `result` contains data to return.
        if (! is_null($this->registry()->get('methods.fetch.result'))) {
            return $this->removeSystemFields($this->registry()->get('methods.fetch.result'));
        }

        // Get Cache ID for current requested entry
        $entryCacheID = $this->getCacheID($this->registry()->get('methods.fetch.params.id'), 'single');
        
        // 1. Try to get current requested entry from the cache.
        if (cache()->has($entryCacheID)) {
            
            // Fetch entry from cache and apply `filterCollection` filter for fetch result
            $this->registry()->set('methods.fetch.result', 
                                   filterCollection(cache()->get($entryCacheID),
                                                    $this->registry()->get('methods.fetch.params.options.filter', [])));

            // Run event
            emitter()->emit('onEntriesFetchSingleCacheHasResult');
            
            // Return result from the cache.
            return collection($this->removeSystemFields($this->registry()->get('methods.fetch.result')));
        }
        
        // 2. Try to get requested entry from the filesystem
        if ($this->has($this->registry()->get('methods.fetch.params.id'))) {

            // Get entry file location
            $entryFile = $this->getFileLocation($this->registry()->get('methods.fetch.params.id'));
            
            // Try to get requested entry from the filesystem.
            $entryFileContent = filesystem()->file($entryFile)->get(true);

            if ($entryFileContent === false) {
                // Run event
                emitter()->emit('onEntriesFetchSingleNoResult');
                return collection($this->removeSystemFields($this->registry()->get('methods.fetch.params.result')));
            }

            // Decode entry file content
            $this->registry()->set('methods.fetch.result', serializers()->{$this->registry()->get('methods.fetch.collection')['serializer']}()->decode($entryFileContent));

            // Run event
            emitter()->emit('onEntriesFetchSingleHasResult');
            
            // Get the result.
            $result = $this->registry()->get('methods.fetch.result');

            // Fields processor
            foreach (collection($result)->dot() as $key => $value) {
                
                $this->registry()->set('methods.fetch.field.key', $key);
                $this->registry()->set('methods.fetch.field.value', $value);
                
                // Run event
                emitter()->emit('onEntriesFetchSingleField');

                $this->registry()->set('methods.fetch.result.' . $this->registry()->get('methods.fetch.field.key'), $this->registry()->get('methods.fetch.field.value'));
            }

            // Apply `filterCollection` filter for fetch result
            $this->registry()->set('methods.fetch.result', filterCollection(collection($this->registry()->get('methods.fetch.result'))->undot(), $this->registry()->get('methods.fetch.params.options.filter', [])));

            // Set cache state
            $cache = $this->registry()->get('methods.fetch.result.cache.enabled',
                                           registry()->get('flextype.settings.cache.enabled'));

            // Save entry data to cache
            if ($cache) {
                cache()->set($entryCacheID, $this->registry()->get('methods.fetch.result'));
            }
            
            // Return entry fetch result
            return collection($this->removeSystemFields($this->registry()->get('methods.fetch.result')));
        }

        // Run event
        emitter()->emit('onEntriesFetchSingleNoResult');

        // Return entry fetch result
        return collection($this->removeSystemFields($this->registry()->get('methods.fetch.result')));
    }

    /**
     * Fetch collection.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @return mixed Returns mixed results from APIs or default is an instance of The Collection class with founded items.
     *
     * @access public
     */
    private function fetchCollection(string $id, array $options = [])
    {
        // Setup registry.
        $this->registry()->set('methods.fetch', [
            'collection' => $this->getCollectionOptions($id),
            'params' => [
                'id' => $id,
                'options' => $options,
            ],
            'result' => null,
        ]);
    
        emitter()->emit('onEntriesFetchCollection');

        // Check if `result` contains data to return.
        if (! is_null($this->registry()->get('methods.fetch.result'))) {
            return $this->removeSystemFields($this->registry()->get('methods.fetch.result'));
        }

        // Determine if collection exists
        if (! $this->has($this->registry()->get('methods.fetch.params.id')) && $this->registry()->get('methods.fetch.params.id') != '') {

            // Run event
            emitter()->emit('onEntriesFetchCollectionNoResult');

            // Return entries array
            return collection($this->registry()->get('methods.fetch.result'));
        }

        // Find entries in the filesystem.
        $entries = find($this->getDirectoryLocation($this->registry()->get('methods.fetch.params.id')),
                        $this->registry()->has('methods.fetch.params.options.find') ?
                        $this->registry()->get('methods.fetch.params.options.find') :
                        []);

        // Walk through entries results
        if ($entries->hasResults()) {

            $data = [];

            foreach ($entries as $currenEntry) {

                $currentEntryID = strings($currenEntry->getPath())
                    ->replace('\\', '/')
                    ->replace(FLEXTYPE_PATH_PROJECT . '/' . $this->options['directory'] . '/', '')
                    ->trim('/')
                    ->toString();

                // Set collection options
                $this->registry()->set('methods.fetch.collection', $this->getCollectionOptions($currentEntryID));

                if ($currenEntry->getType() !== 'file' || $currenEntry->getFilename() !== $this->registry()->get('methods.fetch.collection.filename') . '.' . $this->registry()->get('methods.fetch.collection.extension')) {
                    continue;
                }

                $data[$currentEntryID] = $this->fetchSingle($currentEntryID, [])->toArray();
            }
 
            // Re-init registry after single fetch calls.
            $this->registry()->set('methods.fetch', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'options' => $options,
                ],
                'result' => $data,
            ]);
                
            // Run event
            emitter()->emit('onEntriesFetchCollectionHasResult');

            // Process filter `only` for collection
            // after process we need to unset $options['filter']['only']
            // to avoid it's running inside filterCollection() helper.
            if ($this->registry()->has('methods.fetch.params.options.filter.only')) {
                $data = [];
                foreach ($this->registry()->get('methods.fetch.result', []) as $key => $value) {
                    $data[$key] = collection($value)->only($this->registry()->get('methods.fetch.params.options.filter.only'))->toArray();
                }
                $this->registry()->delete('methods.fetch.params.options.filter.only');
                $this->registry()->set('methods.fetch.result', $data);
            }

            // Process filter `except` for collection
            // after process we need to unset $options['filter']['except']
            // to avoid it's running inside filterCollection() helper.
            if ($this->registry()->has('methods.fetch.params.options.filter.except')) {
                $data = [];
                foreach ($this->registry()->get('methods.fetch.result', []) as $key => $value) {
                    $data[$key] = collection($value)->except($this->registry()->get('methods.fetch.params.options.filter.except'))->toArray();
                }
                $this->registry()->delete('methods.fetch.params.options.filter.except');
                $this->registry()->set('methods.fetch.result', $data);
            }

            // Apply filter `filterCollection` for fetch result data.
            $this->registry()->set('methods.fetch.result', 
                                   filterCollection($this->registry()->get('methods.fetch.result'), 
                                                    $this->registry()->has('methods.fetch.params.options.filter') ? 
                                                    $this->registry()->get('methods.fetch.params.options.filter') : []));
                                                    
            return collection($this->registry()->get('methods.fetch.result'));

        } else {
            // Run event
            emitter()->emit('onEntriesFetchCollectionNoResult');   

            // Return entries array
            return collection($this->registry()->get('methods.fetch.result'));
        }

        // Return entries array
        return collection($this->registry()->get('methods.fetch.result'));
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

        // Setup registry.
        $this->registry()->set('methods.move', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'newID' => $newID,
                ],
                'result' => null,
            ]);    

        // Run event
        emitter()->emit('onEntriesMove');

        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.move.result')) && is_bool($this->registry()->get('methods.move.result'))) {
            return $this->registry()->get('methods.move.result');
        }

        // Check if the original entry exists.
        if (! $this->has($this->registry()->get('methods.move.params.id'))) {
            return false;
        }

        // Do move.
        if (! $this->has($this->registry()->get('methods.move.params.newID'))) {
            return filesystem()
                        ->directory($this->getDirectoryLocation($this->registry()->get('methods.move.params.id')))
                        ->move($this->getDirectoryLocation($this->registry()->get('methods.move.params.newID')));
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
        // Setup registry.
        $this->registry()->set('methods.update', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'data' => $data,
                ],
                'result' => null,
            ]);    
        
        // Run event
        emitter()->emit('onEntriesUpdate');

        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.update.result')) && is_bool($this->registry()->get('methods.update.result'))) {
            return $this->registry()->get('methods.update.result');
        }

        $entryFile = $this->getFileLocation($this->registry()->get('methods.update.params.id'));

        if (filesystem()->file($entryFile)->exists()) {
            $body  = filesystem()->file($entryFile)->get(true);
            $entry = serializers()->{$this->registry()->get('methods.update.collection')['serializer']}()->decode($body);

            return (bool) filesystem()->file($entryFile)->put(serializers()->{$this->registry()->get('methods.update.collection')['serializer']}()->encode(array_merge($entry, $this->registry()->get('methods.update.params.data'))), true);
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
        // Setup registry.
        $this->registry()->set('methods.create', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'data' => $data,
                ],
                'result' => null,
            ]);  
                            
        // Run event
        emitter()->emit('onEntriesCreate');
     
        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.create.result')) && is_bool($this->registry()->get('methods.create.result'))) {
            return $this->registry()->get('methods.create.result');
        }

        // Create entry directory first if it is not exists
        $entryDirectory = $this->getDirectoryLocation($this->registry()->get('methods.create.params.id'));

        if (filesystem()->directory($entryDirectory)->exists()) {
            return false;
        }

        if (! filesystem()->directory($entryDirectory)->create()) {
            return false;
        }
        
        // Create entry file
        $entryFile = $entryDirectory . '/' . $this->registry()->get('methods.create.collection.filename') . '.' . $this->registry()->get('methods.create.collection.extension');
        if (! filesystem()->file($entryFile)->exists()) {
            return (bool) filesystem()->file($entryFile)->put(serializers()->{$this->registry()->get('methods.create.collection')['serializer']}()->encode($this->registry()->get('methods.create.params.data')), true);
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
        // Setup registry.
        $this->registry()->set('methods.delete', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                ],
                'result' => null,
            ]);  

        // Run event
        emitter()->emit('onEntriesDelete');

        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.delete.result')) && is_bool($this->registry()->get('methods.delete.result'))) {
            return $this->registry()->get('methods.delete.result');
        }

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->registry()->get('methods.delete.params.id')))
                    ->delete();
    }

    /**
     * Copy entry.
     *
     * @param string $id    Unique identifier of the entry.
     * @param string $newID New Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
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

        // Setup registry.
        $this->registry()->set('methods.copy', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'newID' => $newID,
                ],
                'result' => null,
            ]);  

        // Run event
        emitter()->emit('onEntriesCopy');

        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.copy.result')) && is_bool($this->registry()->get('methods.copy.result'))) {
            return $this->registry()->get('methods.copy.result');
        }

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->registry()->get('methods.copy.params.id')))
                    ->copy($this->getDirectoryLocation($this->registry()->get('methods.copy.params.newID')));
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
        // Setup registry.
        $this->registry()->set('methods.has', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                ],
                'result' => null,
            ]);  
        
        // Run event
        emitter()->emit('onEntriesHas');

        // Return result from registy `result` if it's value boolean.
        if (! is_null($this->registry()->get('methods.has.result')) && is_bool($this->registry()->get('methods.has.result'))) {
            return $this->registry()->get('methods.has.result');
        }

        return filesystem()->file($this->getFileLocation($this->registry()->get('methods.has.params.id')))->exists();
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
        // Setup registry.
        $this->registry()->set('methods.getFileLocation', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                ],
                'result' => null,
            ]);  

        // Run event
        emitter()->emit('onEntriesGetFileLocation');

        // Return result from registy `result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('methods.getFileLocation.result')) && is_string($this->registry()->get('methods.getFileLocation..result'))) {
            return $this->registry()->get('methods.getFileLocation.result');
        }

        return FLEXTYPE_PATH_PROJECT . '/' . $this->options['directory'] . '/' . $this->registry()->get('methods.getFileLocation.params.id') . '/' . $this->registry()->get('methods.getFileLocation.collection.filename') . '.' . $this->registry()->get('methods.getFileLocation.collection.extension');
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
        // Setup registry.
        $this->registry()->set('methods.getDirectoryLocation', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                ],
                'result' => null,
            ]); 
        
        // Run event
        emitter()->emit('onEntriesGetDirectoryLocation');

        // Return result from registy `result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('methods.getDirectoryLocation.result')) && is_string($this->registry()->get('methods.getDirectoryLocation.result'))) {
            return $this->registry()->get('methods.getDirectoryLocation.result');
        }

        return FLEXTYPE_PATH_PROJECT . '/' . $this->options['directory'] . '/' . $this->registry()->get('methods.getDirectoryLocation.params.id');
    }

    /**
     * Get Cache ID for entry.
     *
     * @param  string $id     Unique identifier of the Cache Entry Item.
     * @param  string $string String to append to the Cache ID.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $id, string $string = ''): string
    {
        // Setup registry.
        $this->registry()->set('methods.getCacheID', [
                'collection' => $this->getCollectionOptions($id),
                'params' => [
                    'id' => $id,
                    'string' => $string . registry()->get('flextype.settings.entries.cache.string'),
                ],
                'result' => null,
            ]); 

        // Run event
        emitter()->emit('onEntriesGetCacheID');

        // Return result from registy `result` if it's not null and it's a string.
        if (! is_null($this->registry()->get('methods.getCacheID.result')) && is_string($this->registry()->get('methods.getCacheID.result'))) {
            return $this->registry()->get('methods.getCacheID.result');
        }
   
        if (registry()->get('flextype.settings.cache.enabled') === false) {
            return '';
        }
    
        $entryFile = $this->getFileLocation($this->registry()->get('methods.getCacheID.params.id'));

        if (filesystem()->file($entryFile)->exists()) {
            return strings($this->options['directory'] . $entryFile . $string . (filesystem()->file($entryFile)->lastModified() ?: ''))->hash()->toString();
        }

        return strings($this->options['directory'] . $entryFile . $string)->hash()->toString();
    }

    /**
     * Get Entries Registry.
     *
     * @return Collection Returns entries registry.
     *
     * @access public
     */
    public function registry(): Collection
    {
        return $this->registry;
    }

    /**
     * Set Entries registry.
     *
     * @return self
     *
     * @access public
     */
    public function setRegistry($registry = null): self 
    {
        $this->registry = collection($registry);

        return $this;
    }

    /**
     * Set Entries options.
     *
     * @return self
     *
     * @access public
     */
    public function setOptions($options = null): self 
    {
        $this->options = collection($options);

        return $this;
    }

    /**
     * Get Entries options.
     *
     * @return Collection Returns entries options.
     *
     * @access public
     */
    public function options(): Collection 
    {
        return $this->options;
    }
}
