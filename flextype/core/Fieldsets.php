<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use function count;
use function rename;

class Fieldsets
{
    /**
     * Flextype Dependency Container
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
     * Fetch fieldset
     *
     * @param string $id Fieldset id
     *
     * @return array|false The entry contents or false on failure.
     *
     * @access public
     */
    public function fetch(string $id)
    {
        $fieldset_file = $this->getFileLocation($id);

        if (Filesystem::has($fieldset_file)) {
            if ($fieldset_body = Filesystem::read($fieldset_file)) {
                if ($fieldset_decoded = Parser::decode($fieldset_body, 'yaml')) {
                    return $fieldset_decoded;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * Fetch all fieldsets
     *
     * @return array
     *
     * @access public
     */
    public function fetchAll() : array
    {
        // Init Fieldsets array
        $fieldsets = [];

        // Get fieldsets files
        $_fieldsets = Filesystem::listContents($this->getDirLocation());

        // If there is any fieldsets file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] !== 'file' || $fieldset['extension'] !== 'yaml') {
                    continue;
                }

                $fieldset_content                 = Parser::decode(Filesystem::read($fieldset['path']), 'yaml');
                $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
            }
        }

        // return fieldsets array
        return $fieldsets;
    }

    /**
     * Rename fieldset
     *
     * @param string $id     Fieldset id
     * @param string $new_id New fieldset id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->getFileLocation($id), $this->getFileLocation($new_id));
    }

    /**
     * Update fieldset
     *
     * @param string $id   Fieldset id
     * @param array  $data Fieldset data to save
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, array $data) : bool
    {
        $fieldset_file = $this->getFileLocation($id);

        if (Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, Parser::encode($data, 'yaml'));
        }

        return false;
    }

    /**
     * Create fieldset
     *
     * @param string $id   Fieldset id
     * @param array  $data Fieldset data to save
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data) : bool
    {
        $fieldset_file = $this->getFileLocation($id);

        if (! Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, Parser::encode($data, 'yaml'));
        }

        return false;
    }

    /**
     * Delete fieldset
     *
     * @param string $id Fieldset id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id) : bool
    {
        return Filesystem::delete($this->getFileLocation($id));
    }

    /**
     * Copy fieldset
     *
     * @param string $id     Fieldset id
     * @param string $new_id New fieldset id
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $new_id) : bool
    {
        return Filesystem::copy($this->getFileLocation($id), $this->getFileLocation($new_id), false);
    }

    /**
     * Check whether fieldset exists.
     *
     * @param string $id Fieldset id
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
     * Helper method getDirLocation
     *
     * @access private
     */
    public function getDirLocation() : string
    {
        return PATH['site'] . '/fieldsets/';
    }

    /**
     * Helper method getFileLocation
     *
     * @param string $id Fieldsets id
     *
     * @access private
     */
    public function getFileLocation(string $id) : string
    {
        return PATH['site'] . '/fieldsets/' . $id . '.yaml';
    }
}
