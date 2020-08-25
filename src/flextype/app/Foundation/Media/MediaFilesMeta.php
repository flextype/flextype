<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation\Media;

use Flextype\Component\Arrays\Arrays;
use Flextype\Component\Filesystem\Filesystem;

class MediaFilesMeta
{
    /**
     * Update file meta information
     *
     * @param string $id    Unique identifier of the file.
     * @param string $field Field name
     * @param string $value Field value
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, string $field, string $value) : bool
    {
        $file_data = flextype('yaml')->decode(Filesystem::read($this->getFileMetaLocation($id)));

        if (Arrays::has($file_data, $field)) {
            Arrays::set($file_data, $field, $value);

            return Filesystem::write($this->getFileMetaLocation($id), flextype('yaml')->encode($file_data));
        }

        return false;
    }

    /**
     * Add file meta information
     *
     * @param string $id    Unique identifier of the file.
     * @param string $field Field name
     * @param string $value Field value
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function add(string $id, string $field, string $value) : bool
    {
        $file_data = flextype('yaml')->decode(Filesystem::read($this->getFileMetaLocation($id)));

        if (! Arrays::has($file_data, $field)) {
            Arrays::set($file_data, $field, $value);

            return Filesystem::write($this->getFileMetaLocation($id), flextype('yaml')->encode($file_data));
        }

        return false;
    }

    /**
     * Delete file meta information
     *
     * @param string $id    Unique identifier of the file.
     * @param string $field Field name
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id, string $field) : bool
    {
        $file_data = flextype('yaml')->decode(Filesystem::read($this->getFileMetaLocation($id)));

        if (Arrays::has($file_data, $field)) {
            Arrays::delete($file_data, $field);

            return Filesystem::write($this->getFileMetaLocation($id), flextype('yaml')->encode($file_data));
        }

        return false;
    }

    /**
     * Get file meta location
     *
     * @param string $id Unique identifier of the file.
     *
     * @return string entry file location
     *
     * @access public
     */
    public function getFileMetaLocation(string $id) : string
    {
        return PATH['project'] . '/uploads/.meta/' . $id . '.yaml';
    }
}
