<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        $fieldset_file = $this->_file_location($id);

        if (Filesystem::has($fieldset_file)) {
            if ($fieldset_body = Filesystem::read($fieldset_file)) {
                if ($fieldset_decoded = JsonParser::decode($fieldset_body)) {
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
        $_fieldsets = Filesystem::listContents($this->_dir_location());

        // If there is any fieldsets file then go...
        if (count($_fieldsets) > 0) {
            foreach ($_fieldsets as $fieldset) {
                if ($fieldset['type'] !== 'file' || $fieldset['extension'] !== 'json') {
                    continue;
                }

                $fieldset_content                 = JsonParser::decode(Filesystem::read($fieldset['path']));
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
        return rename($this->_file_location($id), $this->_file_location($new_id));
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
        $fieldset_file = $this->_file_location($id);

        if (Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, JsonParser::encode($data));
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
        $fieldset_file = $this->_file_location($id);

        if (! Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, JsonParser::encode($data));
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
        return Filesystem::delete($this->_file_location($id));
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
        return Filesystem::copy($this->_file_location($id), $this->_file_location($new_id), false);
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
        return Filesystem::has($this->_file_location($id));
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     */
    private function _dir_location() : string
    {
        return PATH['site'] . '/fieldsets/';
    }

    /**
     * Helper method _file_location
     *
     * @param string $id Fieldsets id
     *
     * @access private
     */
    private function _file_location(string $id) : string
    {
        return PATH['site'] . '/fieldsets/' . $id . '.json';
    }
}
