<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Arr\Arr;
use Intervention\Image\ImageManagerStatic as Image;
use Slim\Http\Environment;
use Slim\Http\Uri;

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
    public function updateMeta(string $id, string $field, string $value) : bool
    {
        $file_data = $this->flextype['serializer']->decode(Filesystem::read($this->getFileMetaLocation($id)), 'yaml');

        if (Arr::keyExists($file_data, $field)) {
            Arr::set($file_data, $field, $value);
            return Filesystem::write($this->getFileMetaLocation($id), $this->flextype['serializer']->encode($file_data, 'yaml'));
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
    public function addMeta(string $id, string $field, string $value) : bool
    {
        $file_data = $this->flextype['serializer']->decode(Filesystem::read($this->getFileMetaLocation($id)), 'yaml');

        if (!Arr::keyExists($file_data, $field)) {
            Arr::set($file_data, $field, $value);
            return Filesystem::write($this->getFileMetaLocation($id), $this->flextype['serializer']->encode($file_data, 'yaml'));
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
    public function deleteMeta(string $id, string $field) : bool
    {
        $file_data = $this->flextype['serializer']->decode(Filesystem::read($this->getFileMetaLocation($id)), 'yaml');

        if (Arr::keyExists($file_data, $field)) {
            Arr::delete($file_data, $field);
            return Filesystem::write($this->getFileMetaLocation($id), $this->flextype['serializer']->encode($file_data, 'yaml'));
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
