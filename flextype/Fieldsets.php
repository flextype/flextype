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
        $fieldsets_file = Fieldsets::_file_location($id);

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

        // If there is any template file then go...
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
     * Rename fieldset
     *
     * @access public
     * @param string $fieldset     Fieldset
     * @param string $new_fieldset New fieldset
     * @return bool True on success, false on failure.
     */
    public function rename(string $fieldset, string $new_fieldset) : bool
    {
        return rename($this->_file_location($fieldset), $this->_file_location($new_fieldset));
    }

    /**
     * Update fieldset
     *
     * @access public
     * @param string $fieldset Fieldset
     * @param array  $data     Data
     * @return bool True on success, false on failure.
     */
    public function update(string $fieldset, array $data) : bool
    {
        $fieldset_file = $this->_file_location($fieldset);

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
     * @param string $fieldset Fieldset
     * @param array  $data     Data
     * @return bool True on success, false on failure.
     */
    public function create(string $fieldset, array $data) : bool
    {
        $fieldset_file = $this->_file_location($fieldset);

        // Check if new entry file exists
        if (!Filesystem::has($fieldset_file)) {
            return Filesystem::write($fieldset_file, JsonParser::encode($data));
        } else {
            return false;
        }
    }

    /**
     * Delete fieldset.
     *
     * @access public
     * @param string $fieldset Fieldset
     * @return bool True on success, false on failure.
     */
    public function delete(string $fieldset) : bool
    {
        return Filesystem::delete($this->_file_location($fieldset));
    }

    /**
     * Copy fieldset
     *
     * @access public
     * @param string $fieldset      Fieldset
     * @param string $new_fieldset  New fieldset
     * @return bool True on success, false on failure.
     */
    public function copy(string $fieldset, string $new_fieldset) : bool
    {
        return Filesystem::copy($this->_file_location($fieldset), $this->_file_location($new_fieldset), false);
    }

    /**
     * Check whether fieldset exists.
     *
     * @access public
     * @param string $fieldset Fieldset
     * @return bool True on success, false on failure.
     */
    public function has(string $fieldset) : bool
    {
        return Filesystem::has($this->_file_location($fieldset));
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
     * @param string $name Name
     * @return string
     */
    private function _file_location(string $name) : string
    {
        return PATH['themes'] . '/' . $this->flextype['registry']->get('settings.theme') . '/fieldsets/' . $name . '.json';
    }
}
