<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

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
     * @access public
     * @param string $id Fieldset id
     * @return array|false The entry contents or false on failure.
     */
    public function fetch(string $id)
    {
        $fieldset_file = $this->_file_location($id);

        if (Filesystem::has($fieldset_file)) {
            if ($fieldset_body = Filesystem::read($fieldset_file)) {
                if ($fieldset_decoded = JsonParser::decode($fieldset_body)) {
                    return $fieldset_decoded;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Fetch all fieldsets
     *
     * @access public
     * @return array
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
                if ($fieldset['type'] == 'file' && $fieldset['extension'] == 'json') {
                    $fieldset_content = JsonParser::decode(Filesystem::read($fieldset['path']));
                    $fieldsets[$fieldset['basename']] = $fieldset_content['title'];
                }
            }
        }

        // return fieldsets array
        return $fieldsets;
    }

    /**
     * Rename fieldset
     *
     * @access public
     * @param string $id     Fieldset id
     * @param string $new_id New fieldset id
     * @return bool True on success, false on failure.
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->_file_location($id), $this->_file_location($new_id));
    }

    /**
     * Update fieldset
     *
     * @access public
     * @param string $id    Fieldset id
     * @param array  $data  Fieldset data to save
     * @return bool True on success, false on failure.
     */
    public function update(string $id, array $data) : bool
    {
        $fieldset_file = $this->_file_location($id);

        if (Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Create fieldset
     *
     * @access public
     * @param string $id    Fieldset id
     * @param array  $data  Fieldset data to save
     * @return bool True on success, false on failure.
     */
    public function create(string $id, array $data) : bool
    {
        $fieldset_file = $this->_file_location($id);

        if (!Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Delete fieldset
     *
     * @access public
     * @param string $id Fieldset id
     * @return bool True on success, false on failure.
     */
    public function delete(string $id) : bool
    {
        return Filesystem::delete($this->_file_location($id));
    }

    /**
     * Copy fieldset
     *
     * @access public
     * @param string $id      Fieldset id
     * @param string $new_id  New fieldset id
     * @return bool True on success, false on failure.
     */
    public function copy(string $id, string $new_id) : bool
    {
        return Filesystem::copy($this->_file_location($id), $this->_file_location($new_id), false);
    }

    /**
     * Check whether fieldset exists.
     *
     * @access public
     * @param string $id Fieldset id
     * @return bool True on success, false on failure.
     */
    public function has(string $id) : bool
    {
        return Filesystem::has($this->_file_location($id));
    }

    /**
     * Helper method _dir_location
     *
     * @access private
     * @return string
     */
    private function _dir_location() : string
    {
        return PATH['site'] . '/fieldsets/';
    }

    /**
     * Helper method _file_location
     *
     * @access private
     * @param string $id Fieldsets id
     * @return string
     */
    private function _file_location(string $id) : string
    {
        return PATH['site'] . '/fieldsets/' . $id . '.json';
    }
}
