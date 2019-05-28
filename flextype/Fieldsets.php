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
     * Fetch fieldsets
     *
     * @access public
     * @param string $id Fieldsets id
     * @return array|false The entry contents or false on failure.
     */
    public function fetch(string $id)
    {
        $fieldsets_file = $this->_file_location($id);

        if (Filesystem::has($fieldsets_file)) {
            if ($fieldsets_body = Filesystem::read($fieldsets_file)) {
                if ($fieldsets_decoded = JsonParser::decode($fieldsets_body)) {
                    return $fieldsets_decoded;
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
     * Fetch Fieldsets for current theme
     *
     * @access public
     * @return array
     */
    public function fetchList() : array
    {
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

        // return fieldsets
        return $fieldsets;
    }

    /**
     * Rename fieldsets
     *
     * @access public
     * @param string $id     Fieldsets id
     * @param string $new_id New fieldsets id
     * @return bool True on success, false on failure.
     */
    public function rename(string $id, string $new_id) : bool
    {
        return rename($this->_file_location($id), $this->_file_location($new_id));
    }

    /**
     * Update fieldsets
     *
     * @access public
     * @param string $id    Fieldsets id
     * @param array  $data  Data to save
     * @return bool True on success, false on failure.
     */
    public function update(string $id, array $data) : bool
    {
        $fieldsets_file = $this->_file_location($id);

        if (Filesystem::has($fieldsets_file)) {
            return Filesystem::write($fieldsets_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Create fieldsets
     *
     * @access public
     * @param string $id    Fieldsets id
     * @param array  $data  Data to save
     * @return bool True on success, false on failure.
     */
    public function create(string $id, array $data) : bool
    {
        $fieldsets_file = $this->_file_location($id);

        if (!Filesystem::has($fieldsets_file)) {
            return Filesystem::write($fieldsets_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Delete fieldsets
     *
     * @access public
     * @param string $id Fieldsets id
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
     * @param string $id      Fieldsets id
     * @param string $new_id  New fieldsets id
     * @return bool True on success, false on failure.
     */
    public function copy(string $id, string $new_id) : bool
    {
        return Filesystem::copy($this->_file_location($id), $this->_file_location($new_id), false);
    }

    /**
     * Check whether fieldsets exists.
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
        return PATH['themes'] . '/' . $this->flextype['registry']->get('settings.theme') . '/fieldsets/';
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
        return PATH['themes'] . '/' . $this->flextype['registry']->get('settings.theme') . '/fieldsets/' . $id . '.json';
    }
}
