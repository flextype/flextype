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
     * Get storage
     *
     * @param  string|int|null $key     Key.
     * @param  mixed           $default Default value.
     */
    public function getStorage($key, $default = null)
    {
        return arrays($this->storage)->get($key, $default);
    }

    /**
     * Set storage
     *
     * @param  string|null $key   Key.
     * @param  mixed       $value Value.
     */
    public function setStorage(?string $key, $value): void
    {
        $this->storage = arrays($this->storage)->set($key, $value)->toArray();
    }

    /**
     * Fetch single entry.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @access public
     */
    public function fetchSingle(string $id, array $options = []): Arrays
    {
        // Store data
        $this->storage['fetch']['id']   = $id;
        $this->storage['fetch']['data'] = [];

        // Run event: onEntryInitialized
        flextype('emitter')->emit('onEntryInitialized');

        // Get Cache ID for current requested entry
        $entryCacheID = $this->getCacheID($this->storage['fetch']['id']);

        // 1. Try to get current requested entry from cache
        if (flextype('cache')->has($entryCacheID)) {
            // Fetch entry from cache
            $this->storage['fetch']['data'] = flextype('cache')->get($entryCacheID);

            // Run event: onEntryAfterCacheInitialized
            flextype('emitter')->emit('onEntryAfterCacheInitialized');

            // Apply filter for fetch data
            $this->storage['fetch']['data'] = filter($this->storage['fetch']['data'], $options);

            // Return entry from cache
            return arrays($this->storage['fetch']['data']);
        }

        // 2. Try to get current requested entry from filesystem
        if ($this->has($this->storage['fetch']['id'])) {
            // Get entry file location
            $entryFile = $this->getFileLocation($this->storage['fetch']['id']);

            // Try to get requested entry from the filesystem
            $entryFileContent = filesystem()->file($entryFile)->get();
            if ($entryFileContent === false) {
                return arrays();
            }

            // Decode entry file content
            $this->storage['fetch']['data'] = flextype('frontmatter')->decode($entryFileContent);

            // Run event: onEntryAfterInitialized
            flextype('emitter')->emit('onEntryAfterInitialized');

            // Set cache state
            $cache = flextype('entries')->storage['fetch']['data']['cache']['enabled'] ??
                                flextype('registry')->get('flextype.settings.cache.enabled');

            // Save entry data to cache
            if ($cache) {
                flextype('cache')->set($entryCacheID, $this->storage['fetch']['data']);
            }

            // Apply filter for fetch data
            $this->storage['fetch']['data'] = filter($this->storage['fetch']['data'], $options);

            // Return entry data
            return arrays($this->storage['fetch']['data']);
        }

        // Return empty array if entry is not founded
        return arrays();
    }

    /**
     * Fetch entries collection.
     *
     * @param string $id      Unique identifier of the entries collecton.
     * @param array  $options Options array.
     *
     * @access public
     */
    public function fetchCollection(string $id, array $options = []): Arrays
    {
        // Store data
        $this->storage['fetch']['id']      = $id;
        $this->storage['fetch']['data']    = [];
        $this->storage['fetch']['options'] = $options;

        // Run event: onEntriesInitialized
        flextype('emitter')->emit('onEntriesInitialized');

        // Find entries
        $entries = find($this->getDirectoryLocation($this->storage['fetch']['id']), $options);

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

            // Restore fetch id
            $this->storage['fetch']['id'] = $id;

            // Apply filter for fetch data
            $this->storage['fetch']['data'] = filter($data, $options);

            // Run event: onEntriesAfterInitialized
            flextype('emitter')->emit('onEntriesAfterInitialized');
        }

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

        // Run event: onEntryMove
        flextype('emitter')->emit('onEntryMove');

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

        // Run event: onEntryUpdate
        flextype('emitter')->emit('onEntryUpdate');

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

        // Run event: onEntryCreate
        flextype('emitter')->emit('onEntryCreate');

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

        // Run event: onEntryDelete
        flextype('emitter')->emit('onEntryDelete');

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

        // Run event: onEntryCopy
        flextype('emitter')->emit('onEntryCopy');

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

        // Run event: onEntryHas
        flextype('emitter')->emit('onEntryHas');

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
