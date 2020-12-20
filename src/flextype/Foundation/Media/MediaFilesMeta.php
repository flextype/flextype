<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation\Media;

use Atomastic\Macroable\Macroable;

use function arrays;
use function filesystem;
use function flextype;

class MediaFilesMeta
{
    use Macroable;

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
    public function update(string $id, string $field, string $value): bool
    {
        $fileData = flextype('serializers')->yaml()->decode(filesystem()->file($this->getFileMetaLocation($id))->get());

        if (arrays($fileData)->has($field)) {
            $fileData = arrays($fileData)->set($field, $value);

            return (bool) filesystem()->file($this->getFileMetaLocation($id))->put(flextype('serializers')->yaml()->encode($fileData->toArray()));
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
    public function add(string $id, string $field, string $value): bool
    {
        $fileData = flextype('serializers')->yaml()->decode(filesystem()->file($this->getFileMetaLocation($id))->get());

        if (! arrays($fileData)->has($field)) {
            $fileData = arrays($fileData)->set($field, $value);

            return (bool) filesystem()->file($this->getFileMetaLocation($id))->put(flextype('serializers')->yaml()->encode($fileData->toArray()));
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
    public function delete(string $id, string $field): bool
    {
        $fileData = flextype('serializers')->yaml()->decode(filesystem()->file($this->getFileMetaLocation($id))->get());

        if (arrays($fileData)->has($field)) {
            $fileData = arrays($fileData)->delete($field);

            return (bool) filesystem()->file($this->getFileMetaLocation($id))->put(flextype('serializers')->yaml()->encode($fileData->toArray()));
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
    public function getFileMetaLocation(string $id): string
    {
        return PATH['project'] . '/media/.meta/' . $id . '.yaml';
    }
}
