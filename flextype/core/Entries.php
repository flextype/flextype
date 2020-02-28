<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Ramsey\Uuid\Uuid;
use function array_replace_recursive;
use function count;
use function date;
use function error_reporting;
use function json_encode;
use function ltrim;
use function md5;
use function rename;
use function rtrim;
use function str_replace;
use function strpos;
use function time;
use function strtotime;

class Entries
{
    /**
     * Current entry data array
     *
     * @var array
     * @access public
     */
    public $entry = [];

    /**
     * Current entries data array
     *
     * @var array
     * @access public
     */
    public $entries = [];

    /**
     * Set Expression
     *
     * @var array
     * @access public
     */
    public $expression = [
        'eq' => Comparison::EQ,
        '=' => Comparison::EQ,

        '<>' => Comparison::NEQ,
        '!=' => Comparison::NEQ,
        'neq' => Comparison::NEQ,

        '<' => Comparison::LT,
        'lt' => Comparison::LT,

        '<=' => Comparison::LTE,
        'lte' => Comparison::LTE,

        '>' => Comparison::GT,
        'gt' => Comparison::GT,

        '>=' => Comparison::GTE,
        'gte' => Comparison::GTE,

        'is' => Comparison::IS,
        'in' => Comparison::IN,
        'nin' => Comparison::NIN,
        'contains' => Comparison::CONTAINS,
        'like' => Comparison::CONTAINS,
        'member_of' => Comparison::MEMBER_OF,
        'start_with' => Comparison::STARTS_WITH,
        'ends_with' => Comparison::ENDS_WITH
    ];

    /**
     * Set Order Direction
     *
     * @var array
     * @access public
     */
    public $direction = [
        'asc' => Criteria::ASC,
        'desc' => Criteria::DESC,
    ];

    /**
     * Set Visibility
     *
     * @var array
     * @access public
     */
    public $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible'
    ];

    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Fetch entry(entries)
     *
     * @param string     $id   Entry ID
     * @param array|null $args Query arguments.
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetch(string $id, $args = null) : array
    {
        // If args is array then it is entries collection request
        if (is_array($args)) {
            return $this->fetchCollection($id, $args);
        } else {
            return $this->fetchSingle($id);
        }
    }

    /**
     * Fetch single entry
     *
     * @param string $id Entry ID
     *
     * @return array The entry array data.
     *
     * @access public
     */
    public function fetchSingle(string $id) : array
    {
        // Get entry file location
        $entry_file = $this->getFileLocation($id);

        // If requested entry file founded then process it
        if (Filesystem::has($entry_file)) {
            // Create unique entry cache_id
            // Entry Cache ID = entry + entry file + entry file time stamp
            if ($timestamp = Filesystem::getTimestamp($entry_file)) {
                $entry_cache_id = md5('entry' . $entry_file . $timestamp);
            } else {
                $entry_cache_id = md5('entry' . $entry_file);
            }

            // Try to get the requested entry from cache
            if ($this->flextype['cache']->contains($entry_cache_id)) {
                // Try to fetch requested entry from the cache
                if ($entry = $this->flextype['cache']->fetch($entry_cache_id)) {
                    // Run event onEntryAfterInitialized
                    $this->flextype['emitter']->emit('onEntryAfterInitialized');

                    // Return entry
                    return $entry;
                }

                // Return empty array
                return [];

                // else Try to get requested entry from the filesystem
            }

            $entry_decoded = $this->flextype['parser']->decode(Filesystem::read($entry_file), 'frontmatter');

            //
            // Add predefined entry items
            //

            // Entry Published At
            $entry_decoded['published_at'] = isset($entry_decoded['published_at']) ? (int) strtotime($entry_decoded['published_at']) : (int) Filesystem::getTimestamp($entry_file);

            // Entry Created At
            $entry_decoded['created_at'] = isset($entry_decoded['created_at']) ? (int) strtotime($entry_decoded['created_at']) : (int) Filesystem::getTimestamp($entry_file);

            // Entry Modified
            $entry_decoded['modified_at'] = (int) Filesystem::getTimestamp($entry_file);

            // Entry Slug
            $entry_decoded['slug'] = isset($entry_decoded['slug']) ? (string) $entry_decoded['slug'] : (string) ltrim(rtrim($id, '/'), '/');

            // Entry Routable
            $entry_decoded['routable'] = isset($entry_decoded['routable']) ? (bool) $entry_decoded['routable'] : true;

            // Entry Visibility
            if (isset($entry_decoded['visibility']) && in_array($entry_decoded['visibility'], $this->visibility)) {
                $entry_decoded['visibility'] = (string) $this->visibility[$entry_decoded['visibility']];
            } else {
                $entry_decoded['visibility'] = (string) $this->visibility['visible'];
            }

            // Save decoded entry content into the cache
            $this->flextype['cache']->save($entry_cache_id, $entry_decoded);

            // Set entry to the Entry class property $entry
            $this->entry = $entry_decoded;

            // Run event onEntryAfterInitialized
            $this->flextype['emitter']->emit('onEntryAfterInitialized');

            // Return entry from the Entry class property $entry
            return $this->entry;
        }

        // Return empty array
        return [];
    }

    /**
     * Fetch entries collection
     *
     * @param string $id Entry ID
     * @param array $args Query arguments.
     *
     * @return array The entries array data.
     *
     * @access public
     */
    public function fetchCollection(string $id, array $args = []) : array
    {
        // Init Entries
        $entries = [];

        // Init Entries
        $this->entries = $entries;

        // Set Expression
        $expression = $this->expression;

        // Set Direction
        $direction = $this->direction;

        // Bind: Entry ID
        $bind_id = $id;

        // Bind: recursive
        $bind_recursive = $args['recursive'] ?? false;

        // Bind: set first result
        $bind_set_first_result = $args['set_first_result'] ?? false;

        // Bind: set max result
        $bind_set_max_result = $args['set_max_result'] ?? false;

        // Bind: where
        $bind_where = [];
        if (isset($args['where']['key']) && isset($args['where']['expr']) && isset($args['where']['value'])) {
            $bind_where['where']['key']   = $args['where']['key'];
            $bind_where['where']['expr']  = $expression[$args['where']['expr']];
            $bind_where['where']['value'] = $args['where']['value'];
        }

        // Bind: and where
        $bind_and_where = [];
        if (isset($args['and_where'])) {
            foreach ($args['and_where'] as $key => $value) {
                if (! isset($value['key']) || ! isset($value['expr']) || ! isset($value['value'])) {
                    continue;
                }

                $bind_and_where[$key] = $value;
            }
        }

        // Bind: or where
        $bind_or_where = [];
        if (isset($args['or_where'])) {
            foreach ($args['or_where'] as $key => $value) {
                if (! isset($value['key']) || ! isset($value['expr']) || ! isset($value['value'])) {
                    continue;
                }

                $bind_or_where[$key] = $value;
            }
        }

        // Bind: order by
        $bind_order_by = [];
        if (isset($args['order_by']['field']) && isset($args['order_by']['direction'])) {
            $bind_order_by['order_by']['field']     = $args['order_by']['field'];
            $bind_order_by['order_by']['direction'] = $args['order_by']['direction'];
        }

        // Get entries path
        $entries_path = $this->getDirLocation($bind_id);

        // Get entries list
        $entries_list = Filesystem::listContents($entries_path, $bind_recursive);

        // If entries founded in entries folder
        if (count($entries_list) > 0) {
            // Entries IDs
            $entries_ids = '';

            // Entries IDs timestamps
            $entries_ids_timestamps = '';

            // Create entries array from entries list and ignore current requested entry
            foreach ($entries_list as $current_entry) {
                if (strpos($current_entry['path'], $bind_id . '/entry' . '.' . 'md') !== false) {
                    // ignore ...
                } else {
                    // We are checking...
                    // Whether the requested entry is a director and whether the file entry is in this directory.
                    if ($current_entry['type'] === 'dir' && Filesystem::has($current_entry['path'] . '/entry.md')) {
                        // Get entry uid
                        // 1. Remove entries path
                        // 2. Remove left and right slashes
                        $uid = ltrim(rtrim(str_replace(PATH['entries'], '', $current_entry['path']), '/'), '/');

                        // For each founded entry we should create $entries array.
                        $entry = $this->fetch($uid);

                        // Add entry into the entries
                        $entries[$uid] = $entry;

                        // Create entries IDs list
                        $entries_ids .= $uid;

                        // Create entries IDs timestamps
                        $entries_ids_timestamps .= Filesystem::getTimestamp($current_entry['path'] . '/entry.md');
                    }
                }
            }

            // Create unique entries $cache_id
            $cache_id =  md5(
                $bind_id .
                             $entries_ids .
                             $entries_ids_timestamps .
                             ($bind_recursive ? 'true' : 'false') .
                             ($bind_set_max_result ? $bind_set_max_result : '') .
                             ($bind_set_first_result ? $bind_set_first_result : '') .
                             json_encode($bind_where) .
                             json_encode($bind_and_where) .
                             json_encode($bind_or_where) .
                             json_encode($bind_order_by)
            );

            // If requested entries exist with a specific cache_id,
            // then we take them from the cache otherwise we look for them.
            if ($this->flextype['cache']->contains($cache_id)) {
                $entries = $this->flextype['cache']->fetch($cache_id);
            } else {
                // Save error_reporting state and turn it off
                // because PHP Doctrine Collections don't works with collections
                // if there is no requested fields to search:
                //      vendor/doctrine/collections/lib/Doctrine/Common/Collections/Expr/ClosureExpressionVisitor.php
                //      line 40: return $object[$field];
                //
                // @todo research this issue and find possible better solution to avoid this in the future
                $oldErrorReporting = error_reporting();
                error_reporting(0);

                // Create Array Collection from entries array
                $collection = new ArrayCollection($entries);

                // Create Criteria for filtering Selectable collections.
                $criteria = new Criteria();

                // Exec: where
                if (isset($bind_where['where']['key']) && isset($bind_where['where']['expr']) && isset($bind_where['where']['value'])) {
                    $expr = new Comparison($bind_where['where']['key'], $bind_where['where']['expr'], $bind_where['where']['value']);
                    $criteria->where($expr);
                }

                // Exec: and where
                if (isset($bind_and_where)) {
                    $_expr = [];
                    foreach ($bind_and_where as $key => $value) {
                        $_expr[$key] = new Comparison($value['key'], $expression[$value['expr']], $value['value']);
                        $criteria->andWhere($_expr[$key]);
                    }
                }

                // Exec: or where
                if (isset($bind_or_where)) {
                    $_expr = [];
                    foreach ($bind_or_where as $key => $value) {
                        $_expr[$key] = new Comparison($value['key'], $expression[$value['expr']], $value['value']);
                        $criteria->orWhere($_expr[$key]);
                    }
                }

                // Exec: order by
                if (isset($bind_order_by['order_by']['field']) && isset($bind_order_by['order_by']['direction'])) {
                    $criteria->orderBy([$bind_order_by['order_by']['field'] => $direction[$bind_order_by['order_by']['direction']]]);
                }

                // Exec: set max result
                if ($bind_set_max_result) {
                    $criteria->setMaxResults($bind_set_max_result);
                }

                // Exec: set first result
                if ($bind_set_first_result) {
                    $criteria->setFirstResult($bind_set_first_result);
                }

                // Get entries for matching criterias
                $entries = $collection->matching($criteria);

                // Gets a native PHP array representation of the collection.
                $entries = $entries->toArray();

                // Restore error_reporting
                error_reporting($oldErrorReporting);

                // Save entries into the cache
                $this->flextype['cache']->save($cache_id, $entries);
            }

            // Set entries into the property entries
            $this->entries = $entries;

            // Run event onEntriesAfterInitialized
            $this->flextype['emitter']->emit('onEntriesAfterInitialized');
        }

        // Return entries
        return $this->entries;
    }

    /**
     * Rename entry
     *
     * @param string $id     Entry ID
     * @param string $new_id New Entry ID
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->getDirLocation($id), $this->getDirLocation($new_id));
    }

    /**
     * Update entry
     *
     * @param string $id   Entry ID
     * @param array  $data Data
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, array $data) : bool
    {
        $entry_file = $this->getFileLocation($id);

        if (Filesystem::has($entry_file)) {
            $body  = Filesystem::read($entry_file);
            $entry = $this->flextype['parser']->decode($body, 'frontmatter');
            return Filesystem::write($entry_file, $this->flextype['parser']->encode(array_merge($entry, $data), 'frontmatter'));
        }

        return false;
    }

    /**
     * Create entry
     *
     * @param string $id   Entry ID
     * @param array  $data Data
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data) : bool
    {
        $entry_dir = $this->getDirLocation($id);

        if (! Filesystem::has($entry_dir)) {
            // Try to create directory for new entry
            if (Filesystem::createDir($entry_dir)) {
                // Check if new entry file exists
                if (! Filesystem::has($entry_file = $entry_dir . '/entry.md')) {
                    $data['uuid']         = Uuid::uuid4()->toString();
                    $data['published_at'] = date($this->flextype->registry->get('flextype.date_format'), time());
                    $data['created_at']   = date($this->flextype->registry->get('flextype.date_format'), time());
                    $data['published_by'] = (Session::exists('uuid') ? Session::get('uuid') : '');
                    $data['created_by']   = (Session::exists('uuid') ? Session::get('uuid') : '');

                    if (isset($data['routable']) && is_bool($data['routable'])) {
                        $data['routable'] = $data['routable'];
                    } else {
                        $data['routable'] = true;
                    }

                    if (isset($data['visibility']) && in_array($data['visibility'], ['visible', 'draft', 'hidden'])) {
                        $data['visibility'] = $data['visibility'];
                    } else {
                        $data['visibility'] = 'visible';
                    }

                    return Filesystem::write($entry_file, $this->flextype['parser']->encode($data, 'frontmatter'));
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Delete entry
     *
     * @param string $id Entry ID
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        return Filesystem::deleteDir($this->getDirLocation($id));
    }

    /**
     * Copy entry(s)
     *
     * @param string $id        Entry id
     * @param string $new_id    New entry id
     * @param bool   $recursive Recursive copy entries.
     *
     * @return bool|null True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id, bool $recursive = false)
    {
        return Filesystem::copy($this->getDirLocation($id), $this->getDirLocation($new_id), $recursive);
    }

    /**
     * Check whether entry exists
     *
     * @param string $id Entry ID
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->getFileLocation($id));
    }

    /**
     * Get entry file location
     *
     * @param string $id Entry ID
     *
     * @return string entry file location
     *
     * @access private
     */
    public function getFileLocation(string $id) : string
    {
        return PATH['entries'] . '/' . $id . '/entry.md';
    }

    /**
     * Get entry directory location
     *
     * @param string $id Entry ID
     *
     * @return string entry directory location
     *
     * @access private
     */
    public function getDirLocation(string $id) : string
    {
        return PATH['entries'] . '/' . $id;
    }
}
