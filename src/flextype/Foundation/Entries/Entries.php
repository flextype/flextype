<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Entries;

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;

use function array_merge;
use function arrays;
use function filesystem;
use function filter;
use function find;
use function flextype;
use function strings;

class Entries
{
    use Macroable;

    /**
     * Entries Storage
     *
     * Used for storing current requested entry(entries) data
     * and maybe changed on fly.
     *
     * @var array
     * @access private
     */
    private $storage = [];

    /**
     * Callback method for fetch.
     *
     * @var string
     * @access private
     */
    private $fetchCallbackMethod = 'single';

    /**
     * Get an item from an storage using "dot" notation.
     *
     * @param  string|int|null $key     Key.
     * @param  mixed           $default Default value.
     *
     * @access public
     *
     * @return array Updated storage.
     */
    public function getStorage($key, $default = null)
    {
        return arrays($this->storage)->get($key, $default);
    }

    /**
     * Checks if the given dot-notated key exists in the storage.
     *
     * @param  string|array $keys Keys
     *
     * @return bool Return TRUE key exists in the array, FALSE otherwise.
     */
    public function hasStorage($keys): bool
    {
        return arrays($this->storage)->has($keys);
    }

    /**
     * Set an storage item to a given value using "dot" notation.
     * If no key is given to the method, the entire storage will be replaced.
     *
     * @param  string|null $key   Key.
     * @param  mixed       $value Value.
     *
     * @access public
     *
     * @return array Updated storage.
     */
    public function setStorage(?string $key, $value): void
    {
        $this->storage = arrays($this->storage)->set($key, $value)->toArray();
    }

    /**
     * Deletes an storage value using "dot notation".
     *
     * @param  array|string $keys Keys
     *
     * @return array Updated storage.
     */
    public function deleteStorage($keys): self
    {
        return $this->storage = arrays($this->storage)->delete($keys)->toArray();
    }

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
    public function fetch(string $id, array $options = [])
    {
        // Store data in EMS.
        $this->setStorage('fetch.id', $id);
        $this->setStorage('fetch.options', $options);
        $this->setStorage('fetch.data', []);

        // Run event: onEntriesFetch
        flextype('emitter')->emit('onEntriesFetch');

        // Get valid callable method for fetch.
        $fetchFromCallbackMethodName = strings($this->hasStorage('fetch.options.from') ?
                                               $this->getStorage('fetch.options.from') :
                                               $this->fetchCallbackMethod)
                                           ->studly()
                                           ->prepend('fetch')
                                           ->toString();

        $fetchFromCallbackMethod = is_callable([$this, $fetchFromCallbackMethodName]) ?
                                                            $fetchFromCallbackMethodName :
                                                            $this->fetchCallbackMethod;

        // Get fetch result
        return $this->{$fetchFromCallbackMethod}($this->getStorage('fetch.id'),
                                                 $this->getStorage('fetch.options'));
    }

    /**
     * Fetch single entry.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @access public
     *
     * @return self Returns instance of The Arrays class.
     */
    public function fetchSingle(string $id, array $options = []): Arrays
    {
        // Store data
        $this->setStorage('fetch.id', $id);
        $this->setStorage('fetch.options', $options);
        $this->setStorage('fetch.data', []);

        // Run event: onEntriesFetchSingle
        flextype('emitter')->emit('onEntriesFetchSingle');

        // Get Cache ID for current requested entry
        $entryCacheID = $this->getCacheID($this->getStorage('fetch.id'));

        // 1. Try to get current requested entry from cache
        if (flextype('cache')->has($entryCacheID)) {

            // Fetch entry from cache and Apply filter for fetch data
            $this->storage['fetch']['data'] = filter(flextype('cache')->get($entryCacheID),
                                                     $this->getStorage('fetch.options.filter', []));

            // Run event: onEntriesFetchSingleCacheHasResult
            flextype('emitter')->emit('onEntriesFetchSingleCacheHasResult');

            // Return entry from cache
            return arrays($this->getStorage('fetch.data'));
        }

        // 2. Try to get current requested entry from filesystem
        if ($this->has($this->getStorage('fetch.id'))) {
            // Get entry file location
            $entryFile = $this->getFileLocation($this->getStorage('fetch.id'));

            // Try to get requested entry from the filesystem
            $entryFileContent = filesystem()->file($entryFile)->get();

            if ($entryFileContent === false) {
                // Run event: onEntriesFetchSingleNoResult
                flextype('emitter')->emit('onEntriesFetchSingleNoResult');
                return arrays($this->getStorage('fetch.data'));
            }

            // Decode entry file content
            $this->setStorage('fetch.data', flextype('frontmatter')->decode($entryFileContent));

            // Run event: onEntriesFetchSingleHasResult
            flextype('emitter')->emit('onEntriesFetchSingleHasResult');

            // Apply filter for fetch data
            $this->storage['fetch']['data'] = filter($this->getStorage('fetch.data'),
                                                     $this->getStorage('fetch.options.filter', []));

            // Set cache state
            $cache = flextype('entries')->storage['fetch']['data']['cache']['enabled'] ??
                                 flextype('registry')->get('flextype.settings.cache.enabled');

             // Save entry data to cache
            if ($cache) {
                flextype('cache')->set($entryCacheID, $this->storage['fetch']['data']);
            }

            // Return entry data
            return arrays($this->storage['fetch']['data']);
        }

        // Run event: onEntriesFetchSingleNoResult
        flextype('emitter')->emit('onEntriesFetchSingleNoResult');

        // Return empty array if entry is not founded
        return arrays($this->storage['fetch']['data']);
    }

    /**
     * Fetch entries collection.
     *
     * @param string $id      Unique identifier of the entries collecton.
     * @param array  $options Options array.
     *
     * @access public
     *
     * @return self Returns instance of The Arrays class.
     */
    public function fetchCollection(string $id, array $options = []): Arrays
    {
        // Run event: onEntriesFetchCollection
        flextype('emitter')->emit('onEntriesFetchCollection');

        // Find entries in the filesystem
        $entries = find($this->getDirectoryLocation($id),
                                                    isset($options['find']) ?
                                                          $options['find'] :
                                                          []);

        // Walk through entries results
        if ($entries->hasResults()) {
            foreach ($entries as $currenEntry) {
                if ($currenEntry->getType() !== 'file' || $currenEntry->getFilename() !== 'entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension')) {
                    continue;
                }

                $currentEntryID = strings($currenEntry->getPath())
                                        ->replace('\\', '/')
                                        ->replace(PATH['project'] . '/entries/', '')
                                        ->trim('/')
                                        ->toString();

                $data[$currentEntryID] = $this->fetchSingle($currentEntryID)->toArray();
            }

            $this->storage['fetch']['data'] = $data;

            // Run event: onEntriesFetchCollectionHasResult
            flextype('emitter')->emit('onEntriesFetchCollectionHasResult');

            // Apply filter for fetch data
            $this->storage['fetch']['data'] = filter($this->storage['fetch']['data'],
                                                     isset($options['filter']) ?
                                                           $options['filter'] :
                                                           []);
        }

        // Run event: onEntriesFetchCollectionNoResult
        flextype('emitter')->emit('onEntriesFetchCollectionNoResult');

        // Return entries array
        return arrays($this->storage['fetch']['data']);
    }

    /**
     * Move entry
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
        // Store data
        $this->storage['move']['id']     = $id;
        $this->storage['move']['new_id'] = $newID;

        // Run event: onEntriesMove
        flextype('emitter')->emit('onEntriesMove');

        if (! $this->has($this->storage['move']['new_id'])) {
            return filesystem()
                        ->directory($this->getDirectoryLocation($this->storage['move']['id']))
                        ->move($this->getDirectoryLocation($this->storage['move']['new_id']));
        }

        return false;
    }

    /**
     * Update entry
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
        // Store data
        $this->storage['update']['id']   = $id;
        $this->storage['update']['data'] = $data;

        // Run event: onEntriesUpdate
        flextype('emitter')->emit('onEntriesUpdate');

        $entryFile = $this->getFileLocation($this->storage['update']['id']);

        if (filesystem()->file($entryFile)->exists()) {
            $body  = filesystem()->file($entryFile)->get();
            $entry = flextype('frontmatter')->decode($body);

            return (bool) filesystem()->file($entryFile)->put(flextype('frontmatter')->encode(array_merge($entry, $this->storage['update']['data'])));
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
        // Store data
        $this->storage['create']['id']   = $id;
        $this->storage['create']['data'] = $data;

        // Run event: onEntriesCreate
        flextype('emitter')->emit('onEntriesCreate');

        // Create entry directory first if it is not exists
        $entryDir = $this->getDirectoryLocation($this->storage['create']['id']);

        if (
            ! filesystem()->directory($entryDir)->exists() &&
            ! filesystem()->directory($entryDir)->create()
        ) {
            return false;
        }

        // Create entry file
        $entryFile = $entryDir . '/entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension');
        if (! filesystem()->file($entryFile)->exists()) {
            return (bool) filesystem()->file($entryFile)->put(flextype('frontmatter')->encode($this->storage['create']['data']));
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
        // Store data
        $this->storage['delete']['id'] = $id;

        // Run event: onEntriesDelete
        flextype('emitter')->emit('onEntriesDelete');

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->storage['delete']['id']))
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
        // Store data
        $this->storage['copy']['id']     = $id;
        $this->storage['copy']['new_id'] = $newID;

        // Run event: onEntriesCopy
        flextype('emitter')->emit('onEntriesCopy');

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->storage['copy']['id']))
                    ->copy($this->getDirectoryLocation($this->storage['copy']['new_id']));
    }

    /**
     * Check whether entry exists
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id): bool
    {
        // Store data
        $this->storage['has']['id'] = $id;

        // Run event: onEntriesHas
        flextype('emitter')->emit('onEntriesHas');

        return filesystem()->file($this->getFileLocation($this->storage['has']['id']))->exists();
    }

    /**
     * Get entry file location
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return string entry file location
     *
     * @access public
     */
    public function getFileLocation(string $id): string
    {
        return PATH['project'] . '/entries/' . $id . '/entry' . '.' . flextype('registry')->get('flextype.settings.entries.extension');
    }

    /**
     * Get entry directory location
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return string entry directory location
     *
     * @access public
     */
    public function getDirectoryLocation(string $id): string
    {
        return PATH['project'] . '/entries/' . $id;
    }

    /**
     * Get Cache ID for entry
     *
     * @param  string $id Unique identifier of the entry(entries).
     *
     * @return string Cache ID
     *
     * @access public
     */
    public function getCacheID(string $id): string
    {
        if (flextype('registry')->get('flextype.settings.cache.enabled') === false) {
            return '';
        }

        $entryFile = $this->getFileLocation($id);

        if (filesystem()->file($entryFile)->exists()) {
            return strings('entry' . $entryFile . (filesystem()->file($entryFile)->lastModified() ?: ''))->hash()->toString();
        }

        return strings('entry' . $entryFile)->hash()->toString();
    }
}
