<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Atomastic\Arrays\Arrays;

use function array_merge;
use function arrays;
use function filesystem;
use function filter;
use function find;
use function flextype;
use function strings;

class Entries
{
    /**
     * Entries Registry
     *
     * Local entries registry used for storing current requested
     * entries data and allow to change them on fly.
     *
     * @var Arrays
     * @access private
     */
    private Arrays $registry;

    /**
     * Entries options
     * 
     * collection - Root collection for entries.
     * fields     - Array of fields for entries. 
     * 
     * @var array
     * @access private
     */
    private array $options = [];

    /**
     *  __construct
     */
    public function __construct(array $options = [])
    {
        $this->registry = arrays();
        $this->options  = $options;
        $this->initFields();
    }

    /**
     * Init Entries Fields
     *
     * @return void
     */
    private function initFields(): void 
    {
        if (isset($this->options['fields']) &&
            is_array($this->options['fields']) && 
            count($this->options['fields']) > 0) {
            foreach ($this->options['fields'] as $field) {
                if (isset($field['path'])) {
                    if (file_exists(ROOT_DIR . $field['path'])) {
                        include_once ROOT_DIR . $field['path'];
                    }
                }
            }
        }
    }

    /**
     * Get Entries Registry
     *
     * @return Arrays
     */
    public function registry(): Arrays
    {
        return $this->registry;
    }

    /**
     * Fetch.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @access public
     *
     * @return Arrays Returns instance of The Arrays class with items.
     */
    public function fetch(string $id, array $options = []): Arrays
    {
      // Store data
      $this->registry()->set('fetch.id', $id);
      $this->registry()->set('fetch.options', $options);
      $this->registry()->set('fetch.data', []);


      // Run event
      flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Fetch');

      // Single fetch helper
      $single = function ($id, $options) {

          // Store data
          $this->registry()->set('fetch.id', $id);
          $this->registry()->set('fetch.options', $options);
          $this->registry()->set('fetch.data', []);

          // Run event
          flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchSingle');

          // Get Cache ID for current requested entry
          $entryCacheID = $this->getCacheID($this->registry()->get('fetch.id'));

          // 1. Try to get current requested entry from cache
          if (flextype('cache')->has($entryCacheID)) {

              // Fetch entry from cache and Apply filter for fetch data
              $this->registry()->set('fetch.data', filter(flextype('cache')->get($entryCacheID),
                                                       $this->registry()->get('fetch.options.filter', [])));

              // Run event
              flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchSingleCacheHasResult');
              
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
                  flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchSingleNoResult');
                  return arrays($this->registry()->get('fetch.data'));
              }

              // Decode entry file content
              $this->registry()->set('fetch.data', flextype('serializers')->yaml()->decode($entryFileContent));

              // Run event
              flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchSingleHasResult');

              // Apply filter for fetch data
              $this->registry()->set('fetch.data', filter($this->registry()->get('fetch.data'),
                                                         $this->registry()->get('fetch.options.filter', [])));

              // Set cache state
              $cache = $this->registry()->get('fetch.data.cache.enabled',
                                             flextype('registry')->get('flextype.settings.cache.enabled'));

               // Save entry data to cache
              if ($cache) {
                  flextype('cache')->set($entryCacheID, $this->registry()->get('fetch.data'));
              }
              
              // Return entry data
              return arrays($this->registry()->get('fetch.data'));
          }

          // Run event
          flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchSingleNoResult');

          // Return empty array if entry is not founded
          return arrays($this->registry()->get('fetch.data'));
      };

      if (isset($this->registry['fetch']['options']['collection']) &&
          strings($this->registry['fetch']['options']['collection'])->isTrue()) {

          // Run event
          flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchCollection');

          if (! $this->getDirectoryLocation($id)) {
              // Run event
              flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchCollectionNoResult');

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
                  if ($currenEntry->getType() !== 'file' || $currenEntry->getFilename() !== 'entry.yaml') {
                      continue;
                  }

                  $currentEntryID = strings($currenEntry->getPath())
                                          ->replace('\\', '/')
                                          ->replace(PATH['project'] . '/entries/' . $this->options['collection'] . '/', '')
                                          ->trim('/')
                                          ->toString();

                  $data[$currentEntryID] = $single($currentEntryID, [])->toArray();
              }

              $this->registry()->set('fetch.data', $data);

              // Run event
              flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchCollectionHasResult');

              // Apply filter for fetch data
              $this->registry()->set('fetch.data', filter($this->registry()->get('fetch.data'),
                                                       isset($options['filter']) ?
                                                             $options['filter'] :
                                                             []));
          }

          // Run event:
          flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'FetchCollectionNoResult');
          
          // Return entries array
          return arrays($this->registry()->get('fetch.data'));
      } else {
          
          return $single($this->registry['fetch']['id'],
                         $this->registry['fetch']['options']);
      }
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
        $this->registry()->set('move.id', $id);
        $this->registry()->set('move.newID', $newID);

        // Run event
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Move');

        if (! $this->has($this->registry()->get('move.newID'))) {
            return filesystem()
                        ->directory($this->getDirectoryLocation($this->registry()->get('move.id')))
                        ->move($this->getDirectoryLocation($this->registry()->get('move.newID')));
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
        $this->registry()->set('update.id', $id);
        $this->registry()->set('update.data', $data);

        // Run event
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Update');

        $entryFile = $this->getFileLocation($this->registry()->get('update.id'));

        if (filesystem()->file($entryFile)->exists()) {
            $body  = filesystem()->file($entryFile)->get();
            $entry = flextype('serializers')->yaml()->decode($body);

            return (bool) filesystem()->file($entryFile)->put(flextype('serializers')->yaml()->encode(array_merge($entry, $this->registry()->get('update.data'))));
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
        $this->registry()->set('create.id', $id);
        $this->registry()->set('create.data', $data);

        // Run event
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Create');

        // Create entry directory first if it is not exists
        $entryDir = $this->getDirectoryLocation($this->registry()->get('create.id'));

        if (
            ! filesystem()->directory($entryDir)->exists() &&
            ! filesystem()->directory($entryDir)->create()
        ) {
            return false;
        }

        // Create entry file
        $entryFile = $entryDir . '/entry.yaml';
        if (! filesystem()->file($entryFile)->exists()) {
            return (bool) filesystem()->file($entryFile)->put(flextype('serializers')->yaml()->encode($this->registry()->get('create.data')));
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
        $this->registry()->set('delete.id', $id);

        // Run event
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Delete');

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
        // Store data
        $this->registry()->set('copy.id', $id);
        $this->registry()->set('copy.newID', $newID);

        // Run event
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Copy');

        return filesystem()
                    ->directory($this->getDirectoryLocation($this->registry()->get('copy.id')))
                    ->copy($this->getDirectoryLocation($this->registry()->get('copy.newID')));
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
        $this->registry()->set('has.id', $id);

        // Run event:
        flextype('emitter')->emit('on' . strings($this->options['collection'])->capitalize()->toString() . 'Has');

        return filesystem()->file($this->getFileLocation($this->registry()->get('has.id')))->exists();
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
        return PATH['project'] . '/entries/' . $this->options['collection'] . '/' . $id . '/entry.yaml';
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
        return PATH['project'] . '/entries/' . $this->options['collection'] . '/' . $id;
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
